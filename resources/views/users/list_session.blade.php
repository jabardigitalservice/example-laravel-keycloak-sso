@extends('siap.layout')

@section('content')
<aside style="width:100%">
    <h2>Sesi yang sedang aktif</h2>

    <table>
        <tr>
            <th>IP Address</th>
            <th>Mulai Aktif</th>
            <th>Terakhir Aktif</th>
            <th>Aplikasi yang terhubung</th>
            <th>Aksi</th>
        </tr>
        @foreach ($sessions as $session)
        <tr>
            <td>{{ $session['ipAddress'] }}</td>
            <td>{{ \Carbon\Carbon::createFromTimestampMs($session['start'], 'Asia/Jakarta')->format('d-m-Y H:i:s') }}</td>
            <td>{{ \Carbon\Carbon::createFromTimestampMs($session['lastAccess'], 'Asia/Jakarta')->format('d-m-Y H:i:s') }}</td>
            <td>{{ implode(', ', array_values($session['clients'])) }}</td>
            <td>
                <button type="button" onclick="removeSession('{{ $session['id'] }}')">Keluar dari sesi ini</button>
            </td>
        </tr>
        @endforeach
    </table>

    <script>
    function removeSession(id) {
        var msg = 'Apakah anda yakin ingin keluar dari sesi ini?';
        if (!confirm(msg)) return null;

        fetch(`{{ url('/') }}/sessions/${id}`, {
            'method': 'delete',
            'headers': {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        }).then(res => {
            if (res.ok) {
                alert('berhasil keluar dari sesi tersebut');
                window.location.reload();
            }
        }).catch(e => alert('Terjadi kesalahan'));
    }
    </script>
</aside>
@endsection
