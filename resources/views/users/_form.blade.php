@extends('siap.layout')

@section('content')
<aside style="width:100%">
    @if ($user->name)
    <h2>Edit user {{ $user->name }}</h2>
    @else
    <h2>Create new user</h2>
    @endif

    <a href="{{ route('users.index') }}">Back to user listing</a>

    @if (session('status'))
        <aside>
            {{ session('status') }}
        </aside>
    @endif

    <form method="POST"
          action="{{ $user->name ?
                     route('users.update', $user) :
                     route('users.store')
                  }}"
    >
        @csrf

        @foreach ([ 'name', 'email', 'nik', 'role' ] as $attr_name)
        <label for="input-{{ $attr_name }}">{{ $attr_name }}</label>
        <input type="text"
               id="input-{{ $attr_name }}"
               name="{{ $attr_name }}"
               value="{{ old($attr_name, $user[$attr_name]) }}"
        />
        @endforeach

        @if ($user->name)
        @method('patch')
        @else
        @include('users._password_field')
        @endif

        <button type="submit">
            Submit
        </button>
    </form>
</aside>
@endsection
