@extends('layouts.app')

@section('title','Machine History')

@section('content')

<div class="mb-6 flex items-center justify-between">

    <div>

        <h1 class="text-2xl font-bold">

            {{ $machine->machine_number }}

        </h1>

        <p class="text-slate-500">

            Machine History

        </p>

    </div>

    <a href="{{ route('machine-history.index') }}" class="rounded-lg bg-slate-700 px-4 py-2 text-white">

        ← Back

    </a>

</div>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">

    <div class="rounded-xl bg-white border shadow-sm p-4">
        <div class="text-xs text-slate-500">Machine Type</div>
        <div class="mt-1 text-lg font-semibold">{{ $machine->machine_type }}</div>
    </div>

    <div class="rounded-xl bg-white border shadow-sm p-4">
        <div class="text-xs text-slate-500">Area</div>
        <div class="mt-1 text-lg font-semibold">{{ $machine->area }}</div>
    </div>

    <div class="rounded-xl bg-white border shadow-sm p-4">
        <div class="text-xs text-slate-500">PM Cycle</div>
        <div class="mt-1 text-lg font-semibold">
            {{ $machine->pm_cycle_value }}
            {{ strtoupper($machine->pm_cycle_unit) }}
        </div>
    </div>

    <div class="rounded-xl bg-white border shadow-sm p-4">
        <div class="text-xs text-slate-500">Last PM</div>
        <div class="mt-1 text-lg font-semibold">
            {{ $lastPm ? $lastPm->format('d-m-Y') : '-' }}
        </div>
    </div>

    <div class="rounded-xl bg-white border shadow-sm p-4">
        <div class="text-xs text-slate-500">Next PM</div>
        <div class="mt-1 text-lg font-semibold">
            {{ $nextPm ? $nextPm->format('d-m-Y') : '-' }}
        </div>
    </div>

</div>

<div class="rounded-xl border overflow-hidden">

    <table class="min-w-full">

        <thead class="bg-slate-100">

            <tr>

                <th class="px-4 py-3">

                    Actual Date

                </th>

                <th>

                    Order Number

                </th>

                <th>

                    PIC

                </th>

                <th>

                    Duration

                </th>

                <th>

                    Status

                </th>

                <th>

                    Action

                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($pmHistories as $pm)

            <tr class="border-t">

                <td class="px-4 py-3">

                    {{ Carbon\Carbon::parse($pm->actual_date)->format('d-m-Y') }}

                </td>

                <td>

                    {{ $pm->order_number }}

                </td>

                <td>

                    {{ $pm->pic }}

                </td>

                <td>

                    {{ $pm->duration_formatted }}

                </td>

                <td>

                    {{ $pm->status }}

                </td>

                <td>

                    <a href="{{ route('machine-history.detail', [
        'machineNumber' => $machine->machine_number,
        'pmSchedule' => $pm->id
    ]) }}" class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700">

                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12H9m12 0A9 9 0 1112 3a9 9 0 019 9z" />

                        </svg>

                        Detail

                    </a>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

<div class="mt-6">

    {{ $pmHistories->links() }}

</div>

@endsection