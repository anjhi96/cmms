@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h1 class="text-2xl font-semibold text-slate-800">Create User</h1>
            <p class="mt-1 text-sm text-slate-500">Add a new system user with the appropriate role.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-5 p-6">
            @csrf

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                <input type="text" name="name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Role</label>
                <select name="role" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none">
                    <option value="admin">Admin</option>
                    <option value="planner">Planner</option>
                    <option value="technician">Technician</option>
                </select>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">Save</button>
                <a href="{{ route('users.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection