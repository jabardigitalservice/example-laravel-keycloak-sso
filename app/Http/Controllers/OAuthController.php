<?php
/*
* Kita perlu membuat controller ini (tidak bisa langsung dimasukkan ke
* file route/web.php) karena setting keycloak ini baru ter-load setelah
* loading file route, sehingga setting keycloak ini tidak akan terbaca jika
* ditaruh di router.
*/

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirect()
    {
        // refresh config to avoid stale cache
        config([
          'services.keycloak.client_id' => env('KEYCLOAK_CLIENT_ID'),
        ]);
        return Socialite::driver('keycloak')->redirect();
    }

    public function callback()
    {
        info('Processing authenticated SSO Login request');

        $keycloakUser = Socialite::driver('keycloak')->user();

        session([
            'KEYCLOAK_LOGIN_DETAILS' => $keycloakUser->accessTokenResponseBody,
            'KEYCLOAK_USER_DATA' => $keycloakUser->user,
        ]);

        // parse access token from keycloak using public key from Keycloak's JWK endpoint
        // parseJWTToken method is defined in app\Helpers.php
        $decodedAccessToken = parseJWTToken($keycloakUser->accessTokenResponseBody['access_token']);
        session(['nik' => $decodedAccessToken->nik ?? '-' ]);;

        // Kalau data sudah ada di database, ambil data. kalau tidak ada, buat baru
        $user = User::firstOrCreate([
            'nik' => session('nik'),
        ], [
            'name' => $keycloakUser->user['preferred_username'],
            'email' => $keycloakUser->user['email'],
        ]);

        // selain memeriksa data yang ada di database local, idealnya juga
        // dilakukan sync dengan data pegawai terpusat yang ada di SIAP. Di sini
        // sengaja fungsi ini tidak diaktifkan agar dapat dibandingkan data yang
        // ada di database local yang sudah ada vs data yang didapatkan dari
        // database SIAP
        /*
        $SIAPUserProfile = getCurrentUserProfileFromSIAP();
        if ($SIAPUserProfile) {
            $user->update([
                'name' => $SIAPUserProfile['name'],
                'email' => $SIAPUserProfile['email'],
                'role' => $SIAPUserProfile['role'],
            ]);
        }
        */

        // log user from keycloak into current session
        Auth::login($user);

        // maping session_id dari Keycloak dengan session_id dari Laravel. Ini
        // akan diperlukan nantinya untuk proses "backchannel logout". Lihat
        // lebih lanjut di fungsi `logoutWebhook()` di bawah
        $cacheKey = env('APP_NAME') . ':keycloak_session_id_map:' . getCurrentKeycloakSessionId();
        \Cache::put($cacheKey, \Session::getId() );
        info("Map id $cacheKey with session " . \Cache::get($cacheKey));

        return redirect('/');
    }

    /* there are bug for latest Keycloak Server Version (18+)
     * https://github.com/SocialiteProviders/Providers/issues/859
     */
    public function logout()
    {
        Auth::logout(); // Logout of your app
        $redirectUri = route('home'); // The URL the user is redirected to
        return redirect(Socialite::driver('keycloak')->getLogoutUrl($redirectUri)); // Redirect to Keycloak
    }

    /* process OIDC logout request from Keycloak Backchannel Logout event
     *
     * ref:
     * - https://www.keycloak.org/docs/latest/server_admin/#_oidc-logout
     * - https://openid.net/specs/openid-connect-backchannel-1_0.html#LogoutToken
     */
    public function logoutWebhook(Request $request)
    {
        $logoutToken = $request->logout_token;
        info('logout webhook request received');

        // parse logout token from keycloak using public key from Keycloak's JWK endpoint
        $decoded = parseJWTToken($logoutToken);

        /*
         * Setelah di decode, seharusnya JWT token ini di validasi sesuai
         * standar yang di dokumentasikan di https://openid.net/specs/openid-connect-backchannel-1_0.html#Validation .
         *
         * Namun karena kode ini hanya contoh, untuk mempersingkat maka di
         * fungsi ini validasi di atas tidak di implementasikan.
         */

        $cacheKey = env('APP_NAME') . ':keycloak_session_id_map:' . $decoded->sid;
        $laravelSessionId = \Cache::get($cacheKey);

        info("cache key: $cacheKey , laravel session id: $laravelSessionId");

        \Session::getHandler()->destroy($laravelSessionId);

        return response('ok');
    }

}


