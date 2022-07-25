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
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

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
        $keycloakUser = Socialite::driver('keycloak')->user();

        session([
            'KEYCLOAK_SESSION_ID' => $keycloakUser->accessTokenResponseBody['session_state'],
        ]);

        // upsert user into database
        $user = User::updateOrCreate(
            [
                'email' => $keycloakUser->email,
            ],
            [
                'name' => $keycloakUser->name,
            ],
        );

        // parse access token from keycloak using public key from Keycloak's JWK endpoint
        $decodedAccessToken = $this->parseJWTToken($keycloakUser->accessTokenResponseBody['access_token']);
        session(['nik' => $decodedAccessToken->nik ?? '-' ]);;

        // log user from keycloak into current session
        Auth::login($user);

        // map Keycloak's session_id with Laravel's session_id
        $cacheKey = 'keycloak_session_id_map:' . session('KEYCLOAK_SESSION_ID');
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
        $decoded = $this->parseJWTToken($logoutToken);

        $cacheKey = 'keycloak_session_id_map:' . $decoded->sid;
        $laravelSessionId = \Cache::get($cacheKey);

        info("keycloak session id: $decoded->sid , laravel session id: $laravelSessionId");

        \Session::getHandler()->destroy($laravelSessionId);

        return response('ok');
    }

    public function parseJWTToken($token)
    {
        // parse JWT token from keycloak using public key from Keycloak's JWK endpoint
        $jwks_response = file_get_contents(env('KEYCLOAK_BASE_URL') . '/realms/' . env('KEYCLOAK_REALM') . '/protocol/openid-connect/certs');
        $jwks = json_decode($jwks_response, true);
        return JWT::decode($token, JWK::parseKeySet($jwks));
    }
}


