@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">

        <!-- TITLE -->
        <h1 class="text-2xl font-bold text-gray-800">
            Machine Problem Findings
        </h1>

        <!-- ACTIONS -->
        <div class="flex flex-col md:flex-row md:items-center gap-3">

            <!-- IMPORT FORM -->
            <form action="{{ route('machine-problem-findings.import') }}" method="POST" enctype="multipart/form-data"
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

                    <!-- File Name -->
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

    </div>

    {{-- ALERT ERROR --}}
    @if ($errors->has('file'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-3">
            {{ $errors->first('file') }}
        </div>
    @endif

    {{-- ALERT --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- FILTER --}}
    <form method="GET" class="mb-4 bg-white p-3 rounded shadow flex flex-wrap gap-2">

        {{-- SORT --}}
        <select name="sort" class="border rounded px-3 py-2">

            <option value="">Default Sort</option>

            <option value="category_asc" {{ request('sort') == 'category_asc' ? 'selected' : '' }}>
                Category ↑
            </option>

            <option value="category_desc" {{ request('sort') == 'category_desc' ? 'selected' : '' }}>
                Category ↓
            </option>

            <option value="finding_asc" {{ request('sort') == 'finding_asc' ? 'selected' : '' }}>
                Finding ↑
            </option>

            <option value="finding_desc" {{ request('sort') == 'finding_desc' ? 'selected' : '' }}>
                Finding ↓
            </option>

        </select>

        {{-- SEARCH --}}
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
            class="border rounded px-3 py-2 w-64">

        {{-- CATEGORY --}}
        <select name="category" class="border rounded px-3 py-2">

            <option value="">All Category</option>

            @foreach ($categories as $category)
                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>

                    {{ $category }}

                </option>
            @endforeach

        </select>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

            Filter

        </button>

        <a href="{{ route('machine-problem-findings.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">

            Reset

        </a>

    </form>

    {{-- TABLE --}}
    <div class="bg-white shadow rounded overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-4 py-3 text-left">Category</th>

                    <th class="px-4 py-3 text-left">Finding</th>

                    <th class="px-4 py-3 text-center">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($findings as $finding)
                    <tr class="border-t">

                        <td class="px-4 py-3">

                            {{ $finding->category }}

                        </td>

                        <td class="px-4 py-3">

                            {{ $finding->finding }}

                        </td>

                        <td class="px-4 py-3">

                            <div class="flex justify-center gap-2">

                                <a href="{{ route('machine-problem-findings.edit', $finding->id) }}"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">

                                    Edit

                                </a>

                                <form action="{{ route('machine-problem-findings.destroy', $finding->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this finding?')">

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

                        <td colspan="4" class="text-center py-6 text-gray-500">

                            No data found.

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4">

        {{ $findings->links() }}

    </div>

    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files.length ?
                input.files[0].name :
                'No file chosen';

            document.getElementById('fileName').innerText = fileName;
        }
    </script>
@endsection
