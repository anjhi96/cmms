@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Measurement Master
                </h1>
                <p class="text-sm text-gray-500">
                    Master measurement item berdasarkan tipe mesin
                </p>
            </div>

            <a href="{{ route('machine-measurements.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                + Add Measurement
            </a>

        </div>

        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- FILTER --}}
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

                {{-- FILTER MEASUREMENT --}}
                <select name="measurement" class="border p-2 rounded w-full md:w-64" onchange="this.form.submit()">

                    <option value="">All Measurements</option>

                    @foreach ($measurements->pluck('measurement_item')->unique() as $measurement)
                        <option value="{{ $measurement }}" {{ request('measurement') == $measurement ? 'selected' : '' }}>
                            {{ $measurement }}
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

                    <option value="measurement_asc" {{ request('sort') == 'measurement_asc' ? 'selected' : '' }}>
                        Measurement A-Z
                    </option>

                    <option value="measurement_desc" {{ request('sort') == 'measurement_desc' ? 'selected' : '' }}>
                        Measurement Z-A
                    </option>

                </select>

                {{-- RESET FILTER --}}
                @if (request()->hasAny(['machine_type', 'measurement', 'sort']))
                    <a href="{{ route('machine-measurements.index') }}" class="text-sm text-red-500 hover:underline ml-2">
                        Reset Filter
                    </a>
                @endif

            </form>

        </div>


        {{-- TABLE --}}
        <div class="bg-white rounded-lg shadow overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-100">

                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Machine Type</th>
                        <th class="p-3 text-left">Measurement Item</th>
                        <th class="p-3 text-left">Unit</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($measurements as $measurement)
                        <tr class="border-t">

                            <td class="p-3">
                                {{ $loop->iteration }}
                            </td>

                            <td class="p-3">
                                {{ $measurement->machine_type }}
                            </td>

                            <td class="p-3">
                                {{ $measurement->measurement_item }}
                            </td>

                            <td class="p-3">
                                {{ $measurement->unit }}
                            </td>

                            <td class="p-3 text-center">

                                <div class="flex justify-center gap-2">

                                    <a href="{{ route('machine-measurements.edit', $measurement->id) }}"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                        Edit
                                    </a>

                                    <form action="{{ route('machine-measurements.destroy', $measurement->id) }}"
                                        method="POST" onsubmit="return confirm('Delete this measurement?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center p-5 text-gray-500">
                                No data found
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-4">
            {{ $measurements->appends(request()->query())->links() }}
        </div>

    </div>
@endsection
