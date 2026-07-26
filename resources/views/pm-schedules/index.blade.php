@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">PM Schedule</h1>
            <p class="text-sm text-slate-500">Manage preventive maintenance schedules</p>
        </div>

        <form action="{{ route('pm-schedules.import') }}" method="POST" enctype="multipart/form-data"
            class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 shadow-sm sm:flex-row sm:items-center">
            @csrf
            <div class="flex items-center gap-2">
                <input type="file" id="fileInput" name="file" accept=".csv" class="hidden"
                    onchange="updateFileName(this)">
                <label for="fileInput"
                    class="cursor-pointer rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                    Choose File
                </label>
                <span id="fileName" class="max-w-[180px] truncate text-sm text-slate-500">No file chosen</span>
            </div>
            <button
                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700">
                Import
            </button>
        </form>
    </div>

    @if ($errors->has('file'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('file') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="mb-4 flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
        <input title="Search machine / type..." type="text" name="search" value="{{ request('search') }}"
            placeholder="Search..."
            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 sm:w-40">

        <select name="area" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            <option value="">All Areas</option>
            @foreach ($areas as $area)
                <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}
                </option>
            @endforeach
        </select>

        <select name="machine_type" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            <option value="">All Machine Type</option>
            @foreach ($machineTypes as $type)
                <option value="{{ $type }}" {{ request('machine_type') == $type ? 'selected' : '' }}>
                    {{ $type }}</option>
            @endforeach
        </select>

        <select name="status" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            <option value="">All Status</option>
            <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>OPEN</option>
            <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>IN PROGRESS</option>
            <option value="FINISHED" {{ request('status') == 'FINISHED' ? 'selected' : '' }}>FINISHED</option>
            <option value="FINISHED_ON_TIME" {{ request('status') == 'FINISHED_ON_TIME' ? 'selected' : '' }}>FINISHED ON
                TIME</option>
            <option value="MISSED" {{ request('status') == 'MISSED' ? 'selected' : '' }}>MISSED</option>
        </select>

        <select name="plan_month" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            <option value="">All Month</option>
            @foreach ($months as $m)
                <option value="{{ $m }}" {{ request('plan_month') == $m ? 'selected' : '' }}>
                    {{ $m }}</option>
            @endforeach
        </select>

        <select name="plan_year" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            <option value="">All Year</option>
            @foreach ($years as $y)
                <option value="{{ $y }}" {{ request('plan_year') == $y ? 'selected' : '' }}>
                    {{ $y }}</option>
            @endforeach
        </select>

        <button
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Filter</button>
        <a href="{{ route('pm-schedules.index') }}"
            class="rounded-lg bg-slate-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-600">Reset</a>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Area
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Machine</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Type
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Due
                            Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Month
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Year
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($schedules as $pm)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-600">{{ $pm->area }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $pm->machine_number }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $pm->machine_type }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $pm->plan_date ? \Carbon\Carbon::parse($pm->plan_date)->format('d-m-Y') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $pm->due_date ? \Carbon\Carbon::parse($pm->due_date)->format('d-m-Y') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $pm->plan_month }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $pm->plan_year }}</td>
                            <td class="px-4 py-3">
                                @switch($pm->status)
                                    @case('OPEN')
                                        <span
                                            class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">OPEN</span>
                                    @break

                                    @case('IN_PROGRESS')
                                        <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">IN
                                            PROGRESS</span>
                                    @break

                                    @case('FINISHED')
                                        <span
                                            class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-700">FINISHED</span>
                                    @break

                                    @case('FINISHED_ON_TIME')
                                        <span
                                            class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">FINISHED
                                            ON TIME</span>
                                    @break

                                    @case('MISSED')
                                        <span
                                            class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">MISSED</span>
                                    @break

                                    @default
                                        <span
                                            class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $pm->status }}</span>
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($pm->status == 'OPEN' || $pm->status == 'MISSED')
                                    <a href="{{ route('pm-schedules.edit', $pm->id) }}"
                                        class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-blue-700">Fill
                                        PM</a>
                                @else
                                    <a href="{{ route('pm-schedules.edit', $pm->id) }}"
                                        class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-amber-600">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
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
