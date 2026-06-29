@extends('layouts.app')
@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">

            <h1 class="text-2xl font-bold text-gray-800">
                Machine Big Problem Master
            </h1>

            <a href="{{ route('machine-problems.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                + Add Problem
            </a>

        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white p-4 rounded-lg shadow mb-4">

            <form method="GET" class="flex flex-col md:flex-row md:items-center gap-3">

                {{-- FILTER MACHINE TYPE --}}
                <select name="machine_type" class="border p-2 rounded w-full md:w-64" onchange="this.form.submit()">

                    <option value="">All Machine Type</option>

                    @foreach ($machines->unique('machine_type') as $machine)
                        <option value="{{ $machine->machine_type }}"
                            {{ request('machine_type') == $machine->machine_type ? 'selected' : '' }}>
                            {{ $machine->machine_type }}
                        </option>
                    @endforeach

                </select>

                {{-- FILTER PROBLEM --}}
                <select name="problem" class="border p-2 rounded w-full md:w-64" onchange="this.form.submit()">

                    <option value="">All Problems</option>

                    @foreach ($machineProblems->pluck('problem')->unique() as $problem)
                        <option value="{{ $problem }}" {{ request('problem') == $problem ? 'selected' : '' }}>
                            {{ $problem }}
                        </option>
                    @endforeach

                </select>

                {{-- SORT --}}
                <select name="sort" class="border p-2 rounded w-full md:w-64" onchange="this.form.submit()">

                    <option value="">Default Sort</option>

                    <option value="machine_type_asc" {{ request('sort') == 'machine_type_asc' ? 'selected' : '' }}>
                        Machine Type ↑
                    </option>

                    <option value="machine_type_desc" {{ request('sort') == 'machine_type_desc' ? 'selected' : '' }}>
                        Machine Type ↓
                    </option>

                    <option value="problem_asc" {{ request('sort') == 'problem_asc' ? 'selected' : '' }}>
                        Problem A-Z
                    </option>

                    <option value="problem_desc" {{ request('sort') == 'problem_desc' ? 'selected' : '' }}>
                        Problem Z-A
                    </option>

                </select>

                {{-- RESET FILTER --}}
                @if (request()->hasAny(['machine_type', 'problem', 'sort']))
                    <a href="{{ route('machine-problems.index') }}" class="text-sm text-red-500 hover:underline ml-2">
                        Reset Filter
                    </a>
                @endif

            </form>

        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">

            <table class="w-full text-sm">

                {{-- HEADER --}}
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="text-left p-3">Machine Type</th>
                        <th class="text-left p-3">Big Problem</th>
                        <th class="text-center p-3 w-40">Action</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody>

                    @forelse($machineProblems as $p)
                        <tr class="border-b hover:bg-gray-50">

                            <td class="p-3 font-semibold">
                                {{ $p->machine_type }}
                            </td>

                            <td class="p-3">
                                {{ $p->problem }}
                            </td>

                            <td class="p-3">

                                <div class="flex justify-center gap-2">

                                    <a href="{{ route('machine-problems.edit', $p->id) }}"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                        Edit
                                    </a>

                                    <form action="{{ route('machine-problems.destroy', $p->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this problem?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="3" class="text-center p-6 text-gray-500">
                                No Big Problem data found
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>
@endsection
