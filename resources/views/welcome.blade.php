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

    <article style="width:40%">
        @auth
        <strong>Selamat datang, {{ Auth::user()->name }}</strong>
        <p>
            <button onclick="window.location='{{ route('oauth.logout') }}'">
                Logout from all application in this session
            </button>
        </p>
        @else
        <strong>Anda belum login. Silahkan login terlebih dahulu</strong>
        <p>
            <button onclick="window.location='{{ route('oauth.login') }}'">
                Login with Jabar SSO
            </button>
        </p>
        @endauth

        <p>
            <label for="">Laravel Session Id: </label> {{ \Session::getId() }}
        </p>
        <p>
            <label for="">Keycloak Session Id: </label> {{ session('KEYCLOAK_SESSION_ID') }}
        </p>
    </article>

    <article style="width:60%">
        <h2>Detail terkait user saat ini:</h2>
        @auth
        <h3>
            Info dari keycloak
        </h3>
        <pre>
{{ print_r(session('KEYCLOAK_USER_DATA')) }}

        </pre>
        <h3>
            Data user yang sesuai dari database app ini
        </h3>
        <pre>
        @php
        $localUserData = \App\Models\User::where('nik', session('nik'))->first();
        $localUserData = $localUserData ? $localUserData->toArray() : null;
        echo print_r($localUserData) ;
        @endphp
{{}}
        </pre>
        <h3>
            Data user yang sesuai dari API SIAP
        </h3>
        <pre>
        @php
        try {
            $siapUrl = env('SIAP_BASE_URL') . '/get_user_detail?token=' . session('KEYCLOAK_LOGIN_DETAILS')['access_token'];
            $siapData = file_get_contents($siapUrl);
            echo print_r(json_decode($siapData));
        } catch (Exception $e) {
            echo print_r($e->getMessage());
        }
        @endphp
        </pre>
        @endif
    </article>

  </main>
</body>
</html>
