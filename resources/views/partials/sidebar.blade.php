<aside class="w-64 min-h-screen bg-slate-900 text-white flex flex-col">

    {{-- HEADER --}}
    <div class="px-6 py-5 border-b border-slate-700">
        <div class="text-lg font-bold tracking-wide">
            CMMS MENU
        </div>
        <div class="text-xs text-slate-400 mt-1">
            Maintenance System
        </div>
    </div>

    @php
        $machineActive = request()->routeIs('machines.*');
        $locActive = request()->routeIs('functional-locations.*');
        $sparepartActive = request()->routeIs('spareparts.*');
        $reportActive = request()->routeIs('reports.*');
        $userActive = request()->routeIs('users.*');
        $dashboardActive = request()->routeIs('dashboard');
        $pmScheduleActive = request()->routeIs('pm-schedules.*');
        $problemActive = request()->routeIs('machine-problems.*');
    @endphp

    {{-- MENU --}}
    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">

        {{-- DASHBOARD --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ $dashboardActive ? 'bg-blue-600' : 'hover:bg-slate-800' }}">
            📊 Dashboard
        </a>

        {{-- PM SCHEDULE --}}
        @if (auth()->user()->role == 'PIC' || auth()->user()->role == 'ADMIN')
            <a href="{{ route('pm-schedules.index') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ $pmScheduleActive ? 'bg-blue-600' : 'hover:bg-slate-800' }}">
                🗓️ PM Schedule
            </a>
        @endif

        {{-- MACHINE --}}
        <a href="{{ route('machines.index') }}"
            class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ $machineActive ? 'bg-blue-600' : 'hover:bg-slate-800' }}">
            🏭 Machines
        </a>

        {{-- SPAREPART --}}
        <a href="{{ route('spareparts.index') }}"
            class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ $sparepartActive ? 'bg-blue-600' : 'hover:bg-slate-800' }}">
            🔧 Sparepart
        </a>

        {{-- MACHINE PROBLEM (ADMIN ONLY) --}}
        @if (auth()->user()->role == 'ADMIN')
            <a href="{{ route('machine-problems.index') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ $problemActive ? 'bg-blue-600' : 'hover:bg-slate-800' }}">
                ⚙️ Machine Problem
            </a>
        @endif

        {{-- REPORTS --}}
        @if (auth()->user()->role == 'KOORDINATOR' || auth()->user()->role == 'ADMIN')
            <a href="{{ route('reports.index') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ $reportActive ? 'bg-blue-600' : 'hover:bg-slate-800' }}">
                📈 Reports
            </a>
        @endif

        {{-- USERS --}}
        @if (auth()->user()->role == 'ADMIN')
            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ $userActive ? 'bg-blue-600' : 'hover:bg-slate-800' }}">
                👤 Users
            </a>
        @endif

        {{-- MEASUREMENT MASTER (ADMIN ONLY)    --}}

        @if (auth()->user()->role == 'ADMIN')
            <a href="{{ route('machine-measurements.index') }}"
                class="block px-4 py-2 rounded mb-2 transition
        {{ request()->routeIs('machine-measurements.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">

                📏 Measurement Master

            </a>
        @endif

        {{-- MASTER CHECKLIST --}}
        @if (auth()->user()->role == 'ADMIN' || auth()->user()->role == 'coordinator')
            <a href="{{ route('machine-checklists.index') }}"
                class="block px-4 py-2 rounded mb-2 transition
        {{ request()->routeIs('machine-checklists.*') ? 'bg-blue-100 text-blue-600 font-semibold' : '' }}">



                ✅ Machine Checklist

            </a>
        @endif

        {{-- MACHINE PROBLEM FINDINGS --}}
        @if (auth()->user()->role == 'ADMIN' || auth()->user()->role == 'Coordinator')
            <a href="{{ route('machine-problem-findings.index') }}"
                class="block px-4 py-2 rounded mb-2 transition
        {{ request()->routeIs('machine-problem-findings.*') ? 'bg-gray-700' : '' }}">



                🔎 Problem Findings

            </a>
        @endif

    </nav>

    {{-- FOOTER --}}
    <div class="px-6 py-3 border-t border-slate-700 text-xs text-slate-500">
        v1.0 CMMS System
    </div>

</aside>
