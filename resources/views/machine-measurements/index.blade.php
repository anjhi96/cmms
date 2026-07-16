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

            <div class="flex flex-col md:flex-row md:items-center gap-3">

                <!-- IMPORT FORM -->
                <form action="{{ route('machine-measurements.import') }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col sm:flex-row sm:items-center gap-3 bg-white p-3 rounded shadow border">

                    @csrf

                    <!-- FILE WRAPPER -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">

                        <!-- Hidden Input -->
                        <input type="file" id="fileInput" name="file" accept=".csv" class="hidden"
                            onchange="updateFileName(this)">

                        <!-- Custom Button -->
                        <label for="fileInput"
                            class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-sm px-3 py-2 rounded border">
                            Choose File
                        </label>

                        <!-- File Name Display -->
                        <span id="fileName" class="text-sm text-gray-600 truncate max-w-[200px]">
                            No file chosen
                        </span>

                    </div>

                    <!-- IMPORT BUTTON -->
                    <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded w-full sm:w-auto">
                        Import
                    </button>

                </form>

            </div>
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
                        <th class="p-3 text-left">Standard</th>
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

                            <td class="p-3">
                                {{ $measurement->standard }}
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
                            <td colspan="6" class="text-center p-5 text-gray-500">
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

    <script>
        function validateFile(input) {
            const file = input.files[0];

            if (!file) return;

            const allowed = ['csv'];
            const ext = file.name.split('.').pop().toLowerCase();

            if (!allowed.includes(ext)) {
                alert("File harus .CSV saja!");
                input.value = ""; // reset file
            }
        }
    </script>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'No file chosen';
            document.getElementById('fileName').textContent = fileName;
        }
    </script>
@endsection
