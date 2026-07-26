@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-6">

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Machine Checklist Master
                </h1>

                <p class="text-gray-500">
                    Manage Machine Checklist Master Data
                </p>

            </div>

            <!-- ACTIONS -->
            <div class="flex flex-col md:flex-row md:items-center gap-3">

                <!-- IMPORT FORM -->
                <form action="{{ route('machine-checklists.import') }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col sm:flex-row sm:items-center gap-3 bg-white p-3 rounded shadow border">

                    @csrf

                    <!-- FILE WRAPPER -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">

                        <!-- Hidden Input -->
                        <input type="file" id="fileInput" name="file" accept=".csv,.xlsx,.xls" class="hidden"
                            onchange="updateFileName(this)">

                        <!-- Custom Button -->
                        <label for="fileInput"
                            class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-sm px-3 py-2 rounded border">

                            Choose File

                        </label>

                        <!-- File Name Display -->
                        <span id="fileName" class="text-sm text-gray-600 truncate max-w-50">

                            No file chosen

                        </span>

                    </div>

                    <!-- IMPORT BUTTON -->
                    <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded w-full sm:w-auto">

                        Import

                    </button>

                </form>


            </div>


        </div>
        @if ($errors->has('file'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-3">
                {{ $errors->first('file') }}
            </div>
        @endif

        <!-- FILTER -->
        <form method="GET" class="bg-white rounded-lg shadow p-4 mb-5 flex flex-wrap gap-3">

            <!-- SORT -->
            <select name="sort" class="border rounded px-3 py-2">

                <option value="">Default Sort</option>

                <option value="machine_type_asc" {{ request('sort') == 'machine_type_asc' ? 'selected' : '' }}>
                    Machine Type ↑
                </option>

                <option value="machine_type_desc" {{ request('sort') == 'machine_type_desc' ? 'selected' : '' }}>
                    Machine Type ↓
                </option>

                <option value="section_order_asc" {{ request('sort') == 'section_order_asc' ? 'selected' : '' }}>
                    Checklist Order ↑
                </option>

                <option value="section_order_desc" {{ request('sort') == 'section_order_desc' ? 'selected' : '' }}>
                    Checklist Order ↓
                </option>

                <option value="item_asc" {{ request('sort') == 'item_asc' ? 'selected' : '' }}>
                    Checklist Item ↑
                </option>

                <option value="item_desc" {{ request('sort') == 'item_desc' ? 'selected' : '' }}>
                    Checklist Item ↓
                </option>

            </select>

            <!-- SEARCH -->
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search checklist..."
                class="border rounded px-3 py-2 w-72">

            <!-- MACHINE -->
            <select name="machine_type" class="border rounded px-3 py-2">

                <option value="">All Machine Type</option>

                @foreach ($machineTypes as $machine)
                    <option value="{{ $machine }}" {{ request('machine_type') == $machine ? 'selected' : '' }}>

                        {{ $machine }}

                    </option>
                @endforeach

            </select>

            <!-- SECTION -->
            <select name="section" class="border rounded px-3 py-2">

                <option value="">All Section</option>

                @foreach ($sections as $section)
                    <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>

                        {{ $section }}

                    </option>
                @endforeach

            </select>

            <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 rounded">

                Filter

            </button>

            <a href="{{ route('machine-checklists.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded">

                Reset

            </a>

        </form>

        <!-- TABLE -->

        <div class="bg-white rounded-lg shadow overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="px-4 py-3 text-left">
                            Machine Type
                        </th>

                        <th class="px-4 py-3 text-left">
                            Section
                        </th>

                        <th class="px-4 py-3 text-center">
                            Section Order
                        </th>

                        <th class="px-4 py-3 text-left">
                            Checklist Item
                        </th>

                        <th class="px-4 py-3 text-left">
                            Maintenance Type
                        </th>

                        <th class="px-4 py-3 text-center">
                            Item Order
                        </th>

                        <th class="px-4 py-3 text-center">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($checklists as $checklist)
                        <tr class="border-t hover:bg-gray-50">

                            <td class="px-4 py-3">
                                {{ $checklist->machine_type }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $checklist->section }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ $checklist->section_order }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $checklist->checklist_item }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $checklist->maintenance_type }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ $checklist->item_order }}
                            </td>

                            <td class="px-4 py-3">

                                <div class="flex justify-center gap-2">

                                    <a href="{{ route('machine-checklists.edit', $checklist->id) }}"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">

                                        Edit

                                    </a>

                                    <form action="{{ route('machine-checklists.destroy', $checklist->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this checklist?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">

                                            Delete

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center text-gray-500 py-8">

                                No checklist found.

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <!-- PAGINATION -->

        <div class="mt-5">

            {{ $checklists->withQueryString()->links() }}

        </div>

    </div>

    <script>
        function updateFileName(input) {

            const fileName = input.files.length ?
                input.files[0].name :
                "No file chosen";

            document.getElementById("fileName").innerText = fileName;

        }
    </script>
@endsection
