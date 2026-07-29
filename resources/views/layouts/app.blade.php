<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CMMS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-screen overflow-hidden bg-slate-100 text-slate-800">
    @php
    $routeName = Route::currentRouteName() ?? 'dashboard';
    $defaultPageTitle = match (true) {
    str_contains($routeName, 'pm-schedules') => 'PM Schedules',
    str_contains($routeName, 'machines') => 'Machines',
    str_contains($routeName, 'spareparts') => 'Spareparts',
    str_contains($routeName, 'machine-measurements') => 'Measurements',
    str_contains($routeName, 'machine-checklists') => 'Checklists',
    str_contains($routeName, 'machine-problems') => 'Problem Categories',
    str_contains($routeName, 'machine-problem-findings') => 'Problem Findings',
    str_contains($routeName, 'users') => 'Users',
    str_contains($routeName, 'reports') => 'Reports',
    str_contains($routeName, 'import') => 'Import',
    default => 'Dashboard',
    };

    $pageTitle = View::hasSection('title') ? View::getSection('title') : $defaultPageTitle;
    @endphp

    <div x-data="{
        collapsed: window.innerWidth < 1024,
        openGroup: 'main'
    }" @resize.window="collapsed = window.innerWidth < 1024" class="flex h-screen overflow-hidden">

        @include('partials.sidebar')

        <div class="flex min-h-0 flex-1 flex-col transition-all duration-300" :class="collapsed ? 'ml-20' : 'ml-72'">
            @include('partials.topbar', ['pageTitle' => $pageTitle])

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                        @yield('content')
                    </div>
                </div>
            </main>

        </div>

    </div>
</body>

</html>