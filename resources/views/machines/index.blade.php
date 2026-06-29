@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <!-- TITLE -->
        <h1 class="text-2xl font-bold text-gray-800">
            Machine Master
        </h1>

        <!-- ACTIONS -->
        <div class="flex flex-col md:flex-row md:items-center gap-3">

            <!-- IMPORT FORM -->
            <form action="{{ route('machines.import') }}" method="POST" enctype="multipart/form-data"
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

            {{-- <!-- NEW BUTTON -->
            <a href="{{ route('machines.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded shadow text-center">
                + Add Machine
            </a> --}}

        </div>
    </div>

    <form method="GET" class="mb-4 bg-white p-3 rounded shadow flex flex-wrap gap-2">
        <!-- SORT -->
        <select name="sort" class="border p-2 rounded">

            <option value="">Default Sort</option>

            <option value="machine_number_asc" {{ request('sort') == 'machine_number_asc' ? 'selected' : '' }}>
                Machine No ↑
            </option>

            <option value="machine_number_desc" {{ request('sort') == 'machine_number_desc' ? 'selected' : '' }}>
                Machine No ↓
            </option>

            <option value="area_asc" {{ request('sort') == 'area_asc' ? 'selected' : '' }}>
                Area A-Z
            </option>

            <option value="area_desc" {{ request('sort') == 'area_desc' ? 'selected' : '' }}>
                Area Z-A
            </option>

        </select>
        <!-- SEARCH -->
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search machine..."
            class="border p-2 rounded w-64">

        <!-- MACHINE TYPE -->
        <select name="machine_type" class="border p-2 rounded">
            <option value="">All Type</option>
            @foreach ($machineTypes as $type)
                <option value="{{ $type->machine_type }}"
                    {{ request('machine_type') == $type->machine_type ? 'selected' : '' }}>
                    {{ $type->machine_type }}
                </option>
            @endforeach
        </select>

        <!-- AREA -->
        <select name="area" class="border p-2 rounded">
            <option value="">All Area</option>
            @foreach ($areas as $a)
                <option value="{{ $a->area }}" {{ request('area') == $a->area ? 'selected' : '' }}>
                    {{ $a->area }}
                </option>
            @endforeach
        </select>

        <!-- STATUS -->
        <select name="status" class="border p-2 rounded">
            <option value="">All Status</option>
            <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
            <option value="INACTIVE" {{ request('status') == 'INACTIVE' ? 'selected' : '' }}>INACTIVE</option>
        </select>

        <!-- BUTTON -->
        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Filter
        </button>

        <!-- RESET -->
        <a href="{{ route('machines.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">
            Reset
        </a>



    </form>

    @if (session('success'))
        <div class="bg-green-100 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>
                    <th class="p-3 text-left">Area</th>
                    <th class="p-3 text-left">Machine Type</th>
                    <th class="p-3 text-left">Machine Number</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Action</th>
                </tr>

            </thead>

            <tbody>

                @forelse($machines as $m)
                    <tr class="border-t">

                        <td class="p-3">{{ $m->area }}</td>
                        <td class="p-3">{{ $m->machine_type }}</td>
                        <td class="p-3 font-bold">{{ $m->machine_number }}</td>

                        <td class="p-3">
                            <span
                                class="px-2 py-1 rounded
                    {{ $m->status == 'ACTIVE' ? 'bg-green-200' : 'bg-red-200' }}">
                                {{ $m->status }}
                            </span>
                        </td>

                        <td class="p-3 text-center">

                            <div class="flex gap-2" text-center>

                                <a href="{{ route('machines.edit', $m->id) }}"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('machines.destroy', $m->id) }}" class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button class="bg-red-600 text-white px-3 py-1 rounded"
                                        onclick="return confirm('Delete machine?')">
                                        Delete
                                    </button>

                                </form>
                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center p-6 text-gray-500">
                            No machines found
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4">
        {{ $machines->links() }}
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
