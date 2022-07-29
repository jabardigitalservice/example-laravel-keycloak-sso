@extends('siap.layout')

@section('content')
<aside style="width:100%">
    <h2>Set new password</h2>

    @if (session('status'))
        <aside>
            {{ session('status') }}
        </aside>
    @endif

    <p>Ini adalah contoh fitur reset password. Halaman ini bisa dimodifikasi sesuai kebutuhan, misalnya dengan menambahakn pengaturan kriteria password (panjang minimal password, input password berulang untuk konfirmasi, dst). Halaman ini juga bisa dimodifikasi untuk fitur lupa password (untuk form yang bisa dibuka user setelah menerima link lupa password di email masing2).</p>

    <form method="POST">
        @csrf

        <label for="input-password">Password</label>
        <input type="password"
               id="input-password"
               name="password"
        />
        <button type="button" onclick="toggleShowPassword()">
            show password
        </button>

        <script>
        function toggleShowPassword() {
            var password_input = document.querySelector('#input-password');

            password_input.type = (password_input.type == 'text') ?
                                  'password' :
                                  'text' ;
        }
        </script>

        <button type="submit">
            Submit
        </button>
    </form>
</aside>
@endsection
