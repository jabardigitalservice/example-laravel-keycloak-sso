<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="https://dummyimage.com/70/0fb7c0/0011ff&text={{ config('app.name') }}">
    <link rel="stylesheet" href="{{ asset('mvp.css') }}">

    <meta charset="utf-8">
    <meta name="description" content="My description">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Simulator for SIAP Website</title>
</head>

<body>
    <header>
        <h1>Simulator for SIAP Website</h1>
    </header>

  <main style="display:flex; gap:1rem; padding:1rem">

    <article style="width:70%">
        @auth
        <strong>Selamat datang, {{ Auth::user()->name }}</strong>
        <p>
            <button onclick="window.location='{{ route('oauth.logout') }}'">
                Logout from all application in this session
            </button>
        </p>
        <h3>Details:</h3>
        <pre>
            {{ print_r(Auth::user()->toArray()) }}

            NIK dari keycloak: {{ session('nik') }}
        </pre>
        @else
        <strong>Anda belum login. Silahkan login terlebih dahulu</strong>
        <p>
            <button onclick="window.location='{{ route('oauth.login') }}'">
                Login with Jabar SSO
            </button>
        </p>
        @endauth
    </article>

    <article style="width:40%">
        <p>
            <label for="">Laravel Session Id: </label> {{ \Session::getId() }}
        </p>
        <p>
            <label for="">Keycloak Session Id: </label> {{ session('KEYCLOAK_SESSION_ID') }}
        </p>
    </article>

  </main>
</body>
</html>
