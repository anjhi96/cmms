@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Sparepart Master
                </h1>

                <p class="text-gray-500 text-sm">
                    Manage sparepart master data
                </p>
            </div>

            <!-- ACTIONS -->
            <div class="flex flex-col md:flex-row md:items-center gap-3">

                <!-- IMPORT FORM -->
                <form action="{{ route('spareparts.import') }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col sm:flex-row sm:items-center gap-3 bg-white p-3 rounded shadow border">

                    @csrf

                    <!-- FILE WRAPPER -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">

                        <!-- Hidden Input -->
                        <input type="file" id="sparepartFileInput" name="file" accept=".csv,.xlsx,.xls" class="hidden"
                            onchange="updateSparepartFileName(this)">

                        <!-- Custom Button -->
                        <label for="sparepartFileInput"
                            class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-sm px-3 py-2 rounded border">

                            Choose File

                        </label>

                        <!-- File Name Display -->
                        <span id="sparepartFileName" class="text-sm text-gray-600 truncate max-w-[200px]">

                            No file chosen

                        </span>

                    </div>

                    <!-- IMPORT BUTTON -->
                    <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded w-full sm:w-auto">

                        Import

                    </button>

                </form>
                @if ($errors->has('file'))
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-3">
                        {{ $errors->first('file') }}
                    </div>
                @endif

                <!-- ADD BUTTON -->
                {{-- <a href="{{ route('spareparts.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center">

                    + Add Sparepart

                </a> --}}

            </div>

        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- FILTER --}}
        <form method="GET" class="mb-4 bg-white p-3 rounded shadow flex flex-wrap gap-2">

            <!-- SORT -->
            <select name="sort" class="border p-2 rounded">

                <option value="">Default Sort</option>

                <option value="material_asc" {{ request('sort') == 'material_asc' ? 'selected' : '' }}>
                    Material No ↑
                </option>

                <option value="material_desc" {{ request('sort') == 'material_desc' ? 'selected' : '' }}>
                    Material No ↓
                </option>

                <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>
                    Stock Low → High
                </option>

                <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>
                    Stock High → Low
                </option>

            </select>

            <!-- SEARCH -->
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search material or description..." class="border p-2 rounded w-64">

            <!-- MACHINE TYPE -->
            <select name="machine_type" class="border p-2 rounded">

                <option value="">All Machine Type</option>

                @foreach ($machineTypes as $type)
                    <option value="{{ $type }}" {{ request('machine_type') == $type ? 'selected' : '' }}>

                        {{ $type }}

                    </option>
                @endforeach

            </select>

            <!-- STATUS -->
            <select name="status" class="border p-2 rounded">

                <option value="">All Status</option>

                <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>

                    ACTIVE

                </option>

                <option value="INACTIVE" {{ request('status') == 'INACTIVE' ? 'selected' : '' }}>

                    INACTIVE

                </option>

            </select>

            <!-- SEGMENT -->
            <select name="segment" class="border p-2 rounded">

                <option value="">All Segment</option>

                @foreach ($segments as $segment)
                    <option value="{{ $segment }}" {{ request('segment') == $segment ? 'selected' : '' }}>

                        {{ $segment }}

                    </option>
                @endforeach

            </select>

            <!-- BUTTON -->
            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Filter
            </button>

            <!-- RESET -->
            <a href="{{ route('spareparts.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">

                Reset

            </a>

        </form>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="p-3 text-left">Material No</th>
                        <th class="p-3 text-left">Description</th>
                        <th class="p-3 text-left">Machine Type</th>
                        <th class="p-3 text-left">Stock</th>
                        <th class="p-3 text-left">Unit</th>
                        <th class="p-3 text-left">ROP</th>
                        <th class="p-3 text-left">Location</th>
                        <th class="p-3 text-left">Price</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($spareparts as $sparepart)
                        <tr class="border-t">

                            <td class="p-3">
                                {{ $sparepart->material_number }}
                            </td>

                            <td class="p-3">
                                {{ $sparepart->description }}
                            </td>

                            <td class="p-3">
                                {{ $sparepart->machine_type }}
                            </td>

                            <td class="p-3">

                                @if ($sparepart->stock <= $sparepart->rop)
                                    <span class="text-red-600 font-bold">

                                        {{ $sparepart->stock }}

                                        (LOW)
                                    </span>
                                @else
                                    {{ $sparepart->stock }}
                                @endif

                            </td>

                            <td class="p-3">
                                {{ $sparepart->unit }}
                            </td>

                            <td class="p-3">
                                {{ $sparepart->rop }}
                            </td>

                            <td class="p-3">
                                {{ $sparepart->location }}
                            </td>

                            <td class="p-3">
                                Rp {{ number_format($sparepart->price, 0, ',', '.') }}
                            </td>

                            <td class="p-3" text-center>

                                @if ($sparepart->status == 'ACTIVE')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded">

                                        ACTIVE

                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded">

                                        INACTIVE

                                    </span>
                                @endif

                            </td>

                            <td class="p-3" text-center>

                                <div class="flex gap-2">

                                    <a href="{{ route('spareparts.edit', $sparepart->id) }}"
                                        class="bg-yellow-500 text-white px-3 py-1 rounded">

                                        Edit

                                    </a>

                                    <form action="{{ route('spareparts.destroy', $sparepart->id) }}" method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button onclick="return confirm('Delete sparepart?')"
                                            class="bg-red-600 text-white px-3 py-1 rounded">

                                            Delete

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10" class="text-center p-5 text-gray-500">

                                No Data Found

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-4">

            {{ $spareparts->appends(request()->query())->links() }}

        </div>

    </div>

    <script>
        function updateSparepartFileName(input) {

            const fileName = input.files.length ?
                input.files[0].name :
                'No file chosen';

            document.getElementById('sparepartFileName').innerText = fileName;
        }
    </script>
@endsection
