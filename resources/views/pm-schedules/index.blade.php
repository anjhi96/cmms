@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <!-- TITLE -->
        <h1 class="text-2xl font-bold text-gray-800">
            PM Schedule
        </h1>

        <!-- ACTIONS -->
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <!-- IMPORT FORM -->
            <form action="{{ route('pm-schedules.import') }}" method="POST" enctype="multipart/form-data"
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

    </div>

    {{-- ALERT ERROR --}}
    @if ($errors->has('file'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-3">
            {{ $errors->first('file') }}
        </div>
    @endif

    {{-- ALERT SUCCESS --}}
    @if (session('success'))
        <div class="bg-green-100 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="mb-4 bg-white p-4 rounded shadow flex flex-wrap gap-2 items-center">

        <!-- SEARCH -->
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search machine / type..."
            class="border p-2 rounded w-64">

        <!-- STATUS -->
        <select name="status" class="border p-2 rounded">
            <option value="">All Status</option>
            <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>OPEN</option>
            <option value="IN PROGRESS" {{ request('status') == 'IN PROGRESS' ? 'selected' : '' }}>IN PROGRESS</option>
            <option value="DONE" {{ request('status') == 'DONE' ? 'selected' : '' }}>DONE</option>
        </select>

        <!-- MONTH (SMART ORDER) -->
        <select name="plan_month" class="border p-2 rounded">
            <option value="">All Month</option>
            @foreach ($months as $m)
                <option value="{{ $m }}" {{ request('plan_month') == $m ? 'selected' : '' }}>
                    {{ $m }}
                </option>
            @endforeach
        </select>

        <!-- YEAR (DESC ORDER) -->
        <select name="plan_year" class="border p-2 rounded">
            <option value="">All Year</option>
            @foreach ($years as $y)
                <option value="{{ $y }}" {{ request('plan_year') == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endforeach
        </select>

        <!-- BUTTON -->
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Filter
        </button>

        <!-- RESET -->
        <a href="{{ route('pm-schedules.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
            Reset
        </a>

    </form>

    <div class="bg-white rounded shadow overflow-hidden">

        <!-- TABLE LIST PM SCHEDULE -->
        <div class="bg-white rounded shadow overflow-x-auto">

            <table class="w-full text-sm text-left">

                <!-- HEADER -->
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-3">Machine</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Month</th>
                        <th class="p-3">Year</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>
                </thead>

                <!-- BODY -->
                <tbody class="divide-y">

                    @foreach ($schedules as $pm)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="p-3 font-medium text-gray-800">
                                {{ $pm->machine_number }}
                            </td>

                            <td class="p-3 text-gray-600">
                                {{ $pm->machine_type }}
                            </td>

                            <td class="p-3 text-gray-600">
                                {{ $pm->plan_date ? \Carbon\Carbon::parse($pm->plan_date)->format('d-m-Y') : '-' }}
                            </td>

                            <td class="p-3 text-gray-600">
                                {{ $pm->plan_month }}
                            </td>

                            <td class="p-3 text-gray-600">
                                {{ $pm->plan_year }}
                            </td>

                            <td class="p-3">
                                @if ($pm->status == 'OPEN')
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">
                                        OPEN
                                    </span>
                                @elseif($pm->status == 'DONE')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
                                        DONE
                                    </span>
                                @else
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                                        {{ $pm->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 text-center">
                                <a href="{{ route('pm-schedules.edit', $pm->id) }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">
                                    Fill PM
                                </a>
                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    <div class="mt-4">
        {{ $schedules->links() }}
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
