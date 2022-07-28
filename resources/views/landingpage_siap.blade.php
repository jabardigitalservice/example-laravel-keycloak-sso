@php
$color_choices = [
  'orange' => '#fb8c00',
  'teal' => '#00897b',
  'purple' => '#8e24aa',
  'blue' => '#1e88e5',
  'red' => '#e53935',
];

$style_color = $color_choices[ env('STYLE_COLOR', 'teal') ];
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="https://dummyimage.com/70/0fb7c0/0011ff&text=SIAP">
    <link rel="stylesheet" href="{{ asset('mvp.css') }}">

    <meta charset="utf-8">
    <meta name="description" content="My description">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Simulator for SIAP Website</title>

    <style>
:root {
    --color: {{ $style_color }};
}
    </style>
</head>

<body style="background: {{ $style_color }}">
    <main style="background: {{ $style_color }}">
        <section style="background:#fff">
            <header>
                <h1>Simulator for SIAP Website</h1>
                <p>Section Subheading</p>
            </header>
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
Status User: {{ isAdmin(session('nik')) ? 'ADMIN' : 'BUKAN ADMIN' }}
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
                    <label for="">Keycloak Session Id: </label> {{ session('KEYCLOAK_SESSION_ID') }}
                </p>
            </aside>
        </section>
  </main>
</body>
</html>
