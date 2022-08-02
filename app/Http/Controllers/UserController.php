<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Services\KeycloakAdmin;

class UserController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('getUserDetail');
        $this->middleware(function ($request, $next) {
            if (!isAdmin($request->user()))
                abort(401);

            return $next($request);
        })->except('getUserDetail', 'listSession', 'removeSession', 'resetPassword');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();
        return view('users._form', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::create($request->all());

        if (!$user)
            abort('500');
        else
            $request->session()->flash('status', "Creating user successful!");

        // create new user on keycloak
        try {
            $result = KeycloakAdmin::getClient()->createUser([
                'username' => $user->name,
                'email' => $user->email,
                'enabled' => true,
                'emailVerified' => true,
                'credentials' => [
                    [
                        'type'=>'password',
                        'value'=> $request->password,
                    ],
                ],
                'attributes' => [
                    'nik' => $user->nik,
                ],
            ]);
        } catch (Exception $e) {
            $request->session()->flash('status', "Error when creating Keycloak User: {$e->getMessage()}");
        }

        return redirect()->route('users.edit', $user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users._form', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($user->update($request->all())) {
            $request->session()->flash('status', "Updating user $user->name successful!");
        }

        return redirect()->route('users.edit', $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        info("Removing user with NIK $user->nik");

        $keycloakUserId = getKeycloakUserId($user);

        // di sini idealnya juga dilakukan proses logout semua session aktif
        // untuk user ini di semua aplikasi. Di contoh ini dilewatkan untuk
        // mempersingkat kode.

        // remove user from keycloak
        $result = KeycloakAdmin::getClient()->deleteUser([
            'id' => $keycloakUserId,
        ]);

        if (empty($result)) {
            info("Failed removing user $keycloakUserId from Keycloak");
            abort(500, 'Failed removing user from Keycloak');
        }

        info('Remove succeed');

        return $user->delete();
    }

    // menampilkan data user pemilik NIk yang tercantum di dalam JWT token
    public function getUserDetail(Request $request)
    {
        $decodedAccessToken = parseJWTToken($request->token);

        return response()->json(
            User::where('nik', $decodedAccessToken->nik)
                ->first()
        );
    }

    public function resetPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = $request->user();

            info("Resetting password for user with NIK $user->nik");

            $keycloakUserId = getKeycloakUserId($user);

            // actual keycloak password reset
            $result = KeycloakAdmin::getClient()->resetUserPassword([
                'id' => $keycloakUserId,

                // credential representation object
                // references:
                // - https://stackoverflow.com/a/35014705
                // - https://www.keycloak.org/docs-api/12.0/rest-api/index.html#_resetpassword
                'type' => 'password',
                'temporary' => false,
                'value' => $request->password,
            ]);

            if ($result)
                $request->session()->flash('status', "Password change successful!");
        }

        return view('users.reset_password');
    }

    public function listSession(Request $request)
    {
        $keycloakUserId = getKeycloakUserId($request->user());

        // actual keycloak password reset
        $sessions = KeycloakAdmin::getClient()->getUserSessions([
            'id' => $keycloakUserId,
        ]);

        return view('users.list_session', compact('sessions'));
    }

    public function removeSession(Request $request, $id)
    {
        info("removing session with id $id");

        // actual keycloak password reset
        $result = KeycloakAdmin::getClient()->revokeUserSession([
            'session' => $id,
            'realms' => env('KEYCLOAK_REALM'),
        ]);

        if ($result) return true;
    }
}
