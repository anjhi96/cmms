@extends('layouts.app')

@section('title','Machine History')

@section('content')

<div class="flex items-center justify-between mb-6">

    <div>

        <h1 class="text-2xl font-bold">
            Machine History
        </h1>

        <p class="text-slate-500">
            Preventive Maintenance History
        </p>

    </div>

</div>

<div class="overflow-x-auto rounded-xl border">

<table class="min-w-full">

    <thead class="bg-slate-100">

        <tr>

            <th class="px-4 py-3 text-left">
                Machine Number
            </th>

            <th class="px-4 py-3 text-left">
                Machine Type
            </th>

            <th class="px-4 py-3 text-left">
                Area
            </th>

            <th class="px-4 py-3 text-left">
                Last PM
            </th>

            <th class="px-4 py-3 text-center">
                Action
            </th>

        </tr>

    </thead>

    <tbody>

    @forelse($machines as $machine)

        <tr class="border-t">

            <td class="px-4 py-3">

                {{ $machine->machine_number }}

            </td>

            <td class="px-4 py-3">

                {{ $machine->machine_type }}

            </td>

            <td class="px-4 py-3">

                {{ $machine->area }}

            </td>

            <td class="px-4 py-3">

                {{ $machine->last_pm
                    ? \Carbon\Carbon::parse($machine->last_pm)->format('d-m-Y')
                    : '-' }}

            </td>

            <td class="px-4 py-3 text-center">

                <button
                    disabled
                    class="rounded-lg bg-slate-300 px-4 py-2 text-white cursor-not-allowed">

                    View History

                </button>

            </td>

        </tr>

    @empty

        <tr>

            <td colspan="5" class="text-center py-10 text-slate-500">

                No machine history found.

            </td>

        </tr>

    @endforelse

    </tbody>

</table>

</div>

<div class="mt-6">

    {{ $machines->links() }}

</div>

@endsection