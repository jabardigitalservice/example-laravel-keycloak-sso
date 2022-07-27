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
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} | Example Laravel-Keycloak SSO Integration</title>
    <link rel="icon" href="https://dummyimage.com/70/{{ substr($style_color,1) }}/000&text={{ config('app.name') }}">

     <!-- Pico.css (Classless version) -->
    <link rel="stylesheet" href="{{ asset('pico.classless.min.css') }}">

    <style>
/* Orange Light scheme (Default) */
/* Can be forced with data-theme="light" */
[data-theme="light"],
:root:not([data-theme="dark"]) {
  --primary: {{ $style_color }};
  --primary-hover: {{ $style_color }};
  --primary-focus: {{ $style_color }};
  --primary-inverse: #FFF;
}

    </style>
</head>
<body style="background:var(--primary)">
 <!-- Header -->
  <header style="background:white; padding:1rem">
      <hgroup>
        <h1>Welcome to: {{ config('app.name') }}</h1>
        <h3>Example Laravel-Keycloak SSO Integration</h3>
      </hgroup>
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
