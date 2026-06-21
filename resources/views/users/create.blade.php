@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Create User
</h1>

<div class="bg-white p-6 rounded shadow">

<form action="{{ route('users.store') }}" method="POST">
    @csrf

    <div class="mb-4">
        <label>Name</label>
        <input type="text" name="name"
               class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Email</label>
        <input type="email" name="email"
               class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Password</label>
        <input type="password" name="password"
               class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Role</label>
        <select name="role"
                class="w-full border p-2 rounded">

            <option value="admin">Admin</option>
            <option value="planner">Planner</option>
            <option value="technician">Technician</option>

        </select>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Save
    </button>

</form>

</div>

@endsection