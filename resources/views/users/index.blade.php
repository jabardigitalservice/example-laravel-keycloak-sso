@extends('siap.layout')

@section('content')
<aside style="width:100%">
    <h2>Daftar user</h2>

    <a href="{{ route('users.create') }}">Tambah User</a>
    <table>
        <header>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Role</th>
                <th>NIK</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </header>
        @foreach ($users as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->role }}</td>
                <td>{{ $user->nik }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('users.edit', $user) }}">Edit</a> |
                    <a onclick="deleteUser('{{$user->id}}', '{{$user->name}}')">Delete</a>
                </td>
            </tr>
        @endforeach

        <script>
            function deleteUser(id, name) {
                if (!confirm(`Are you sure you want to delete user '${name}'?`))
                    return false;

                fetch('{{ route('users.index') }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                }).then(res => {
                    if (res.ok) {
                        alert('delete succeed');
                        window.location.reload();
                    } else {
                        alert('delete failed');
                    }
                }).catch(e => alert('Encounter error'));
            }
        </script>
    </table>

    {{ $users->links() }}
</aside>
@endsection
