<aside class="w-64 bg-slate-900 text-white">

    <div class="mb-4 text-lg font-bold">
        CMMS MENU
    </div>

    @php
        $machineActive = request()->routeIs('machines.*');
        $locActive = request()->routeIs('functional-locations.*');
        $sparepartActive = request()->routeIs('spareparts.*');
        $reportActive = request()->routeIs('reports.*');
        $userActive = request()->routeIs('users.*');
        $dashboardActive = request()->routeIs('dashboard');
        $pmScheduleActive = request()->routeIs('pm-schedules.*');
    @endphp

    <nav class="p-4 space-y-2">

        <a href="{{ route('dashboard') }}"
            class="block px-4 py-2 rounded mb-2 transition
           {{ $dashboardActive ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
            Dashboard
        </a>

        @if (auth()->user()->role == 'PIC' || auth()->user()->role == 'ADMIN')
            <a href="{{ route('pm-schedules.index') }}"
                class="block px-4 py-2 rounded mb-2 transition
               {{ $pmScheduleActive ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                PM Schedule
            </a>
        @endif

        {{-- MACHINE --}}
        <a href="{{ route('machines.index') }}"
            class="block px-4 py-2 rounded mb-2 transition
       {{ $machineActive ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
            Machines
        </a>

        {{-- SPAREPART --}}
        <a href="{{ route('spareparts.index') }}"
            class="block px-4 py-2 rounded mb-2 transition
       {{ $sparepartActive ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
            Sparepart
        </a>

        {{-- REPORTS --}}
        @if (auth()->user()->role == 'KOORDINATOR' || auth()->user()->role == 'ADMIN')
            <a href="{{ route('reports.index') }}"
                class="block px-4 py-2 rounded mb-2 transition
               {{ $reportActive ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Reports
            </a>
        @endif

        {{-- USERS --}}
        @if (auth()->user()->role == 'ADMIN')
            <a href="{{ route('users.index') }}"
                class="block px-4 py-2 rounded mb-2 transition
               {{ $userActive ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Users
            </a>
        @endif

    </nav>

</aside>
