@extends('siap.layout')

@section('content')
<header style="text-align:left">
    <details open="">
        <summary>Penjelasan terkait web ini</summary>
        <p>
            Ini adalah website simulasi dari fungsionalitas di web SIAP. <strong>Web ini hanyalah contoh</strong>, dan tidak dimaksudkan untuk meniru persis fungsi yang ada di SIAP, namun hanya sebagai gambaran bagaimana ketika nanti SIAP sudah melakukan integrasi dengan sistem SSO Jabar.
        </p>
        <p>
            Beberapa asumsi yang digunakan dalam implementasi web ini:
        </p>

        <ol>
            <li>SIAP nantinya juga akan menggunakan SSO Jabar sebagai sistem loginnya</li>
            <li>SIAP akan menjadi "source of truth" terkait data kepegawaian Pemprov Jabar untuk aplikasi-aplikasi Pemprov Jabar</li>
        </ol>
    </details>
</header>
<aside style="width:40%">
   @auth
    <h2>Selamat datang, {{ Auth::user()->name }}</h2>
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

    <h3>Session Details</h3>
    <details>
        <summary>Laravel/PHP Session ID</summary>
        <p>Ini adalah ID dari sesi bawaan dari PHP. Setiap client yang mengunjungi web ini akan memiliki Session ID unik, yang nantinya ID ini bisa digunakan untuk proses "backchannel logout" dari Keycloak. Info lebih lanjut bisa cek referensi berikut:</p>
        <ul>
            <li><a target="_blank" href="https://www.php.net/manual/en/features.session.security.management.php">Dokumentasi terkait manajemen Session di PHP</a></li>
            <li><a target="_blank" href="https://stackoverflow.com/questions/56863876/how-to-logout-user-from-specific-session-in-laravel/56864262#56864262">Diskusi stackoverflow terkait logout session Laravel secara remote</a></li>
        </ul>
    </details>
    <p>
        {{ \Session::getId() }}
    </p>
    <details>
        <summary>Keycloak Session ID</summary>
        <p>Ini adalah ID dari sesi login yang digenerate oleh Keycloak. Guna ID ini agar ketika logout, website ini bisa mencocokkan sesi ID dari laravel yang perlu dihapuskan berdasarkan request logout dari server Keycloak. Info lebih lanjut bisa cek source code di <code>App\Http\Controllers\OAuthController.php</code> di metode <code>logoutWebhook()</code></p>
    </details>
    <p>
        {{ getCurrentKeycloakSessionId() ?: '-' }}
    </p>
</aside>
<aside style="width:40%">
    <h3>Detail user:</h3>
    @auth
    <pre>
{{ print_r(Auth::user()->toArray()) }}

NIK dari keycloak: {{ session('nik') }}
Status User: {{ isAdmin(Auth::user()) ? 'ADMIN' : 'BUKAN ADMIN' }}
    </pre>
    @endauth
</aside>
@endsection
