@extends('siap.layout')

@section('content')
<aside style="width:40%">
    @auth
    <h2>Selamat datang, {{ Auth::user()->name }}</h2>
    <p>
        <button onclick="window.location='{{ route('oauth.logout') }}'">
            Logout from all application in this session
        </button>
    </p>
    <h3>Details:</h3>
    <pre>
{{ print_r(Auth::user()->toArray()) }}

NIK dari keycloak: {{ session('nik') }}
Status User: {{ isAdmin(Auth::user()) ? 'ADMIN' : 'BUKAN ADMIN' }}
    </pre>
    @else
    <strong>Anda belum login. Silahkan login terlebih dahulu</strong>
    <p>
        <button onclick="window.location='{{ route('oauth.login') }}'">
            Login with Jabar SSO
        </button>
    </p>
    @endauth
</aside>
<aside style="width:40%">
    <h3>Session Details</h3>
    <p>
        <label for="">Laravel Session Id: </label> {{ \Session::getId() }}
    </p>
    <p>
        <label for="">Keycloak Session Id: </label> {{ getCurrentKeycloakSessionId() ?: '-' }}
    </p>
</aside>
@endsection
