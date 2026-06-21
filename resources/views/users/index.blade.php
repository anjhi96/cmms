@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-2xl font-bold">
        User Management
    </h1>

    <a href="{{ route('users.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded">

        + Add User

    </a>

</div>

@if(session('success'))
<div class="bg-green-100 p-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded shadow overflow-hidden">

<table class="w-full">

    <thead class="bg-gray-100">

        <tr>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Email</th>
            <th class="p-3 text-left">Role</th>
        </tr>

    </thead>

    <tbody>

        @foreach($users as $user)

        <tr class="border-t">

            <td class="p-3">{{ $user->name }}</td>
            <td class="p-3">{{ $user->email }}</td>
            <td class="p-3">
                <span class="px-2 py-1 bg-blue-100 rounded">
                    {{ $user->role }}
                </span>
            </td>

        </tr>

        @endforeach

    </tbody>

</table>

</div>

<div class="mt-4">
    {{ $users->links() }}
</div>

@endsection