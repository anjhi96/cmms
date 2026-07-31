@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">
    Fill PM Record
</h1>



<div class="bg-linear-to-r from-blue-600 to-blue-700 text-white rounded-xl p-6 shadow mb-6">

    <h2 class="text-xl font-bold mb-4">
        Machine Information
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div>
            <p class="text-sm opacity-80">Machine Number</p>
            <p class="font-semibold text-lg">
                {{ $pmSchedule->machine_number }}
            </p>
        </div>

        <div>
            <p class="text-sm opacity-80">Machine Type</p>
            <p class="font-semibold text-lg">
                {{ $pmSchedule->machine_type }}
            </p>
        </div>

        <div>
            <p class="text-sm opacity-80">Last PM Date</p>
            <p class="font-semibold text-lg">
                {{ $lastPm ? $lastPm->format('d-m-Y') : '-' }}
            </p>
        </div>

    </div>

</div>

<div class="bg-white rounded-xl shadow p-6 mb-6">

    <h3 class="text-lg font-semibold mb-6">
        PM Information
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label name="order_number" class="block mb-2 text-sm font-medium">
                Order Number
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->order_number }}

            </div>

        </div>

        <div>
            <label name="actual_date" class="block mb-2 text-sm font-medium">
                Actual Date
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->actual_date }}

            </div>
        </div>

        <div>
            <label name="pic" class="block mb-2 text-sm font-medium">
                PIC PM
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->pic }}

            </div>
        </div>

        <div>
            <label name="start_time" class="block mb-2 text-sm font-medium">
                Start PM
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->start_time }}

            </div>
        </div>

        <div>
            <label name="end_time" class="block mb-2 text-sm font-medium">
                End PM
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->end_time }}

            </div>
        </div>

        <div>
            <label name="duration" class="block mb-2 text-sm font-medium">
                Duration (Hours)
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->duration }}

            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

        {{-- Tampilkan hanya untuk type tertentu --}}
        @if ($pmSchedule->requiresOilChange())
        <div>
            <label class="block mb-2 text-sm font-medium">
                Oil Change
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->oil_change }}

            </div>
        </div>
        @endif

        <div>
            <label class="block mb-2 text-sm font-medium">
                Greasing
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->greasing }}

            </div>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">
                WO ZSBP
            </label>

            <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

                {{ $pmSchedule->wo_zsbp }}

            </div>
        </div>

    </div>
    <div class="gap-3 mt-5">
        <label class="block mb-2 text-sm font-medium">
            Remarks
        </label>

        <div class="mt-1 rounded-lg border bg-slate-50 p-3 font-semibold">

            {{ $pmSchedule->remarks }}

        </div>
    </div>

</div>

<div class="bg-white rounded-xl shadow p-6 mb-6">

    <h3 class="text-lg font-semibold mb-6">
        PM Result
    </h3>
    <div id="problem-wrapper">


        <div class="bg-white rounded-lg shadow p-6 mb-6">

            <h2 class="text-lg font-semibold mb-4">
                Problems
            </h2>

            @if($pmProblems->isEmpty())

            <div class="text-slate-500 italic">
                No problem found during this PM.
            </div>

            @else

            <div class="overflow-x-auto">

                <table class="min-w-full border border-slate-200 rounded-lg">

                    <thead class="bg-slate-100">

                        <tr>

                            <th class="px-4 py-3 text-left">Problem</th>
                            <th class="px-4 py-3 text-left">Finding</th>
                            <th class="px-4 py-3 text-center">Severity</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($pmProblems as $problem)

                        <tr class="border-t">

                            <td class="px-4 py-3">

                                {{ $problem->machineProblem->problem ?? '-' }}

                            </td>

                            <td class="px-4 py-3">

                                {{ $problem->machineProblemFinding->finding ?? '-' }}

                            </td>

                            <td class="px-4 py-3 text-center">

                                @php
                                $color = match($problem->severity){
                                'High' => 'bg-red-100 text-red-700',
                                'Medium' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-green-100 text-green-700'
                                };
                                @endphp

                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                    {{ $problem->severity }}
                                </span>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            @endif

        </div>

    </div>

</div>


{{-- PM Measurement --}}

<div class="bg-white rounded-lg shadow p-6 mb-6">

    <h2 class="text-lg font-semibold mb-4">
        Measurements
    </h2>

    <div class="overflow-x-auto">

        <table class="min-w-full border">

            <thead class="bg-slate-100">

                <tr>

                    <th class="px-4 py-3 text-left">
                        Measurement Item
                    </th>

                    <th class="px-4 py-3 text-left">
                        Result
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($pmMeasurements as $measurement)

                <tr class="border-t">

                    <td class="px-4 py-3">

                        {{ $measurement->measurement_item }}

                    </td>

                    <td class="px-4 py-3 font-semibold">

                        {{ $measurement->measurement_value }}

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

{{-- Sparepart Usage --}}

<div class="bg-white rounded-lg shadow p-6 mb-6">

    <div class="flex items-center justify-between mb-4">

        <h2 class="text-lg font-semibold">
            Sparepart Usage
        </h2>

        <div class="text-right">

            <p class="text-sm text-slate-500">
                Total Cost
            </p>

            <p class="text-xl font-bold text-emerald-600">
                USD {{ number_format($totalCost, 2) }}
            </p>

        </div>

    </div>

    @if($pmSpareparts->isEmpty())

    <div class="rounded-lg border border-dashed border-slate-300 py-8 text-center text-slate-500">

        No spareparts used during this PM.

    </div>

    @else

    <div class="overflow-x-auto">

        <table class="min-w-full border border-slate-200 rounded-lg">

            <thead class="bg-slate-100">

                <tr>

                    <th class="px-4 py-3 text-left">
                        Material Number
                    </th>

                    <th class="px-4 py-3 text-left">
                        Description
                    </th>

                    <th class="px-4 py-3 text-center">
                        Qty
                    </th>

                    <th class="px-4 py-3 text-center">
                        Unit
                    </th>

                    <th class="px-4 py-3 text-right">
                        Price
                    </th>

                    <th class="px-4 py-3 text-right">
                        Total
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($pmSpareparts as $item)

                @php
                $price = $item->sparepart->price ?? 0;
                $qty = $item->qty ?? 0;
                $lineTotal = $qty * $price;
                @endphp

                <tr class="border-t hover:bg-slate-50">

                    <td class="px-4 py-3 font-medium">
                        {{ $item->sparepart->material_number }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $item->sparepart->description }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        {{ $qty }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        {{ $item->sparepart->unit }}
                    </td>

                    <td class="px-4 py-3 text-right">
                        USD {{ number_format($price, 2) }}
                    </td>

                    <td class="px-4 py-3 text-right font-semibold">
                        USD {{ number_format($lineTotal, 2) }}
                    </td>

                </tr>

                @endforeach

            </tbody>

            <tfoot class="bg-slate-50 border-t-2">

                <tr>

                    <td colspan="5" class="px-4 py-3 text-right font-bold">

                        Total Cost

                    </td>

                    <td class="px-4 py-3 text-right text-emerald-600 font-bold text-lg">

                        USD {{ number_format($totalCost, 2) }}

                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

    @endif

</div>

<div class="bg-white rounded-xl shadow p-6">

    <h2 class="text-xl font-semibold mb-5">

        PM Checklist

    </h2>

    @foreach($checklists as $section => $items)

    <div x-data="{ open: true }" class="mb-4 border rounded-xl overflow-hidden">

        <button @click="open=!open"
            class="w-full flex justify-between items-center bg-slate-100 px-5 py-4 hover:bg-slate-200 transition">

            <div class="flex items-center gap-3">

                <span class="font-semibold">

                    {{ $section }}

                </span>

                <span class="text-xs bg-slate-300 rounded-full px-2 py-1">

                    {{ $items->count() }} Items

                </span>

            </div>

            <svg class="w-5 h-5 transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />

            </svg>

        </button>

        <div x-show="open" x-transition class="border-t border-slate-200">

            <div class="overflow-x-auto">
                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-4 py-3 text-left">
                                Checklist Item
                            </th>

                            <th class="px-4 py-3 text-center">
                                Result
                            </th>

                            <th class="px-4 py-3 text-left">
                                Remarks
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($items as $item)

                        <tr class="border-t hover:bg-slate-50">

                            <td class="px-4 py-3">

                                {{ $item->machineChecklist->checklist_item }}

                            </td>

                            <td class="px-4 py-3">

                                <div class="flex flex-wrap gap-2 justify-center">

                                    @forelse($item->results as $result)

                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $result['badge'] }}">

                                        {{ $result['text'] }}

                                    </span>

                                    @empty

                                    <span class="text-slate-400">

                                        -

                                    </span>

                                    @endforelse

                                </div>

                            </td>

                            </td>

                            <td class="px-4 py-3">

                                {{ $item->remarks ?: '-' }}

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    @endforeach

</div>



<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
@endsection