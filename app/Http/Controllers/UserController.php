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
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!isAdmin($request->user()))
                abort(401);

            return $next($request);
        });
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
            KeycloakAdmin::getClient()->createUser([
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
        // remove user from keycloak

        return $user->delete();
    }
}
