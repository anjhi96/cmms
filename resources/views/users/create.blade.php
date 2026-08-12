@extends('layouts.app')

@section('content')
<div>
    <label class="mb-2 block text-sm font-medium text-slate-700">
        Role
    </label>

    <select
        name="role"
        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none"
        required
    >
        <option value="">Select Role</option>

        @foreach ($roles as $role)
            <option
                value="{{ $role }}"
                @selected(old('role') === $role)
            >
                {{ $role }}
            </option>
        @endforeach
    </select>

    @error('role')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
@endsection