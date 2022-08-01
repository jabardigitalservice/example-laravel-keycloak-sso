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
    <link rel="icon" href="https://dummyimage.com/70x70/{{ substr($style_color,1) }}/0011ff&text=SIAP">
    <link rel="stylesheet" href="{{ asset('mvp.css') }}">

    <meta charset="utf-8">
    <meta name="description" content="My description">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Simulator for SIAP Website')</title>

    <style>
:root {
    --color: {{ $style_color }};
}
    </style>
</head>

<body style="background: {{ $style_color }}">
    <header style="background:#fff; height:2rem">
        <nav>
            <div>
                <h1>Simulator for SIAP Website</h1>
            </div>

            <ul>
                @auth
                <li>Halo, {{ Auth::user()->name }}</li>

                    @if (isAdmin(Auth::user()))
                    <li>
                        <a href="{{ route('users.index') }}">
                            Manage Users
                        </a>
                    </li>
                    @endif
                <li>
                    <a href="{{ route('users.list_session') }}">
                        List sessions
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.reset_password') }}">
                        Reset password
                    </a>
                </li>
                <li>
                    <a href="{{ route('oauth.logout') }}">
                        Logout
                    </a>
                </li>
                @else
                <li>
                    <a href="{{ route('oauth.login') }}">
                        Login with Jabar SSO
                    </a>
                </li>
                @endauth
            </ul>
        </nav>
    </header>

    <main style="background: {{ $style_color }}">
        <section style="background:#fff">
            @yield('content')
        </section>
  </main>
</body>
</html>
