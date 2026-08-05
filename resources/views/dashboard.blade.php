@extends('layouts.app')

@section('content')

<div class="space-y-6">
    {{-- KPI Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        @include('partials.dashboard-kpi-card', [
            'title' => 'PM Completion (%)',
            'value' => '72%',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>'
        ])

        @include('partials.dashboard-kpi-card', [
            'title' => 'Target Machine (Current Month)',
            'value' => '120',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h3m10 0h3a1 1 0 001-1V7M7 7V5a2 2 0 012-2h6a2 2 0 012 2v2"/></svg>'
        ])

        @include('partials.dashboard-kpi-card', [
            'title' => 'Actual Machine (Current Month)',
            'value' => '86',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2v4h6v-4c0-1.105-1.343-2-3-2zM6 20h12"/></svg>'
        ])

        @include('partials.dashboard-kpi-card', [
            'title' => 'Remaining PM',
            'value' => '34',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>'
        ])

        @include('partials.dashboard-kpi-card', [
            'title' => 'Overdue PM',
            'value' => '7',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12A9 9 0 1112 3a9 9 0 019 9z"/></svg>'
        ])
    </div>

    {{-- Main content grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            {{-- PM Completion Progress Card --}}
            @include('partials.dashboard-progress-card', [
                'percentage' => '72%','percent_value' => 72, 'target' => 120, 'actual' => 86, 'remaining' => 34
            ])

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- PM Completion Trend (chart placeholder) --}}
                @include('partials.dashboard-chart-card', [
                    'title' => 'PM Completion Trend',
                    'id' => 'pmTrendChart',
                    'filters' => '<select class="border rounded px-2 py-1"><option>2026</option><option>2025</option></select>'
                ])

                {{-- PM Status Breakdown (donut) --}}
                @include('partials.dashboard-donut-card', [
                    'title' => 'PM Status Breakdown',
                    'id' => 'pmStatusDonut',
                    'filters' => '<select class="border rounded px-2 py-1"><option>2026</option></select> <select class="border rounded px-2 py-1"><option>All Months</option></select> <select class="border rounded px-2 py-1"><option>All Areas</option></select>'
                ])
            </div>

            {{-- Completion by Area --}}
            @include('partials.dashboard-area-progress', [
                'areas' => [
                    ['name' => 'WWD','percent' => 98],
                    ['name' => 'C&B','percent' => 90],
                    ['name' => 'Assembly','percent' => 85],
                    ['name' => 'Painting','percent' => 78],
                ]
            ])

            {{-- PM Due Table --}}
            @include('partials.dashboard-pm-due-table', [
                'rows' => [
                    ['area' => 'WWD','day0'=>2,'day1'=>1,'day2'=>0,'total'=>5],
                    ['area' => 'C&B','day0'=>1,'day1'=>2,'day2'=>1,'total'=>6],
                    ['area' => 'Assembly','day0'=>0,'day1'=>1,'day2'=>1,'total'=>3],
                ]
            ])
        </div>

        <div class="space-y-4">
            {{-- Quick Actions --}}
            <div class="grid grid-cols-2 gap-3">
                <a href="#" class="bg-white rounded-xl shadow-sm p-4 flex items-center space-x-3">
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <!-- PM Schedule Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">PM Schedule</div>
                        <div class="text-sm font-medium text-gray-800">Open</div>
                    </div>
                </a>

                <a href="#" class="bg-white rounded-xl shadow-sm p-4 flex items-center space-x-3">
                    <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                        <!-- Scan QR Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h4V3m0 0H3v4m0 0v10a2 2 0 002 2h10v-2H7a2 2 0 01-2-2V7zM21 17h-4v4h4v-4zM17 3h4v4h-4V3zM7 17h10v-4H7v4z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Scan QR</div>
                        <div class="text-sm font-medium text-gray-800">Scanner</div>
                    </div>
                </a>

                <a href="#" class="bg-white rounded-xl shadow-sm p-4 flex items-center space-x-3">
                    <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                        <!-- Reports Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6M7 21h10"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Reports</div>
                        <div class="text-sm font-medium text-gray-800">View</div>
                    </div>
                </a>

                <a href="#" class="bg-white rounded-xl shadow-sm p-4 flex items-center space-x-3">
                    <div class="p-2 bg-pink-50 text-pink-600 rounded-lg">
                        <!-- Machine Master Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2v4h6v-4c0-1.105-1.343-2-3-2zM6 20h12"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Machine Master</div>
                        <div class="text-sm font-medium text-gray-800">Manage</div>
                    </div>
                </a>
            </div>

            {{-- Top 10 Sparepart Usage --}}
            @include('partials.dashboard-top-bars', [
                'title' => 'Top 10 Sparepart Usage',
                'items' => [
                    ['label'=>'Bearing A', 'percent'=>78, 'value'=>'320'],
                    ['label'=>'Bolt M6', 'percent'=>65, 'value'=>'260'],
                    ['label'=>'Filter X', 'percent'=>50, 'value'=>'200'],
                ],
                'filters' => '<select class="border rounded px-2 py-1"><option>2026</option></select>'
            ])

            {{-- Top 10 Problem Categories --}}
            @include('partials.dashboard-top-bars', [
                'title' => 'Top 10 Problem Categories',
                'items' => [
                    ['label'=>'Leakage', 'percent'=>42, 'value'=>'40'],
                    ['label'=>'Vibration', 'percent'=>35, 'value'=>'33'],
                    ['label'=>'Electrical', 'percent'=>28, 'value'=>'20'],
                ],
                'filters' => '<select class="border rounded px-2 py-1"><option>2026</option></select>'
            ])

            {{-- Activity Timeline --}}
            @include('partials.dashboard-timeline', [
                'activities' => [
                    ['time'=>'08:12','machine'=>'M-1001','desc'=>'PM completed by John'],
                    ['time'=>'09:45','machine'=>'M-2034','desc'=>'Reported: oil leakage'],
                    ['time'=>'11:00','machine'=>'M-1100','desc'=>'Inspection done, OK'],
                ]
            ])

            {{-- Today's PM Schedule --}}
            @include('partials.dashboard-today-schedule-table', [
                'schedules' => [
                    ['machine'=>'M-1001','area'=>'WWD','pic'=>'John Doe','due_date'=>'2026-08-05','status'=>'Completed'],
                    ['machine'=>'M-2034','area'=>'C&B','pic'=>'Jane Doe','due_date'=>'2026-08-05','status'=>'Pending'],
                    ['machine'=>'M-1100','area'=>'Assembly','pic'=>'Budi','due_date'=>'2026-08-05','status'=>'Overdue'],
                ]
            ])

        </div>
    </div>
</div>

@endsection
