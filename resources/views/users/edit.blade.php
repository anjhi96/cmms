@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">
            <h1 class="text-2xl font-semibold text-slate-800">
                Edit User
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Update user account, role, or password.
            </p>
        </div>

        <form
            action="{{ route('users.update', $user) }}"
            method="POST"
            class="space-y-5 p-6"
        >
            @csrf
            @method('PUT')

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none"
                    required
                >

                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none"
                    required
                >

                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    New Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none"
                    placeholder="Leave blank to keep current password"
                >

                <p class="mt-1 text-xs text-slate-500">
                    Leave blank if you don't want to change the password.
                </p>

                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Role
                </label>

                @if (auth()->id() === $user->id)

                    <input
                        type="text"
                        value="{{ $user->role }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-medium text-slate-500"
                        disabled
                    >

                    <input
                        type="hidden"
                        name="role"
                        value="{{ $user->role }}"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        You cannot change your own role.
                    </p>

                @else

                    <select
                        name="role"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none"
                        required
                    >
                        @foreach ($roles as $role)
                            <option
                                value="{{ $role }}"
                                @selected(old('role', $user->role) === $role)
                            >
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>

                @endif

                @error('role')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label
                    for="is_active"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Status
                </label>

                @if (auth()->id() === $user->id)

                    <input
                        type="text"
                        value="Active"
                        class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-medium text-slate-500"
                        disabled
                    >

                    <input
                        type="hidden"
                        name="is_active"
                        value="1"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        You cannot deactivate your own account.
                    </p>

                @else

                    <select
                        id="is_active"
                        name="is_active"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none"
                        required
                    >
                        <option
                            value="1"
                            @selected(old('is_active', $user->is_active) == true)
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            @selected(old('is_active', $user->is_active) == false)
                        >
                            Inactive
                        </option>
                    </select>

                @endif

                @error('is_active')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Update User
                </button>

                <a
                    href="{{ route('users.index') }}"
                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection