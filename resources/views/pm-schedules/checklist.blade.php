@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">

        <h1 class="text-2xl font-bold text-gray-800">
            PM Checklist
        </h1>

    </div>

    {{-- Machine Information --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">

        <h2 class="text-lg font-semibold mb-4">
            Machine Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="text-sm text-gray-500">
                    Machine Number
                </label>

                <div class="font-semibold">
                    {{ $pmSchedule->machine_number }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    Machine Type
                </label>

                <div class="font-semibold">
                    {{ $pmSchedule->machine_type }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    Order Number
                </label>

                <div class="font-semibold">
                    {{ $pmSchedule->order_number }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    PIC PM
                </label>

                <div class="font-semibold">
                    {{ $pmSchedule->pic }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    Actual Date
                </label>

                <div class="font-semibold">
                    {{ $pmSchedule->actual_date }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    Duration
                </label>

                <div class="font-semibold">
                    {{ $pmSchedule->duration }} Minutes
                </div>
            </div>

        </div>

    </div>

    {{-- Checklist --}}
    <form action="{{ route('pm-schedules.checklist.save', $pmSchedule) }}" method="POST">

        @csrf

        @php
            $currentSection = '';
        @endphp

        @foreach ($checklists as $i => $item)

            @if ($currentSection != $item->section)

                @php
                    $currentSection = $item->section;
                @endphp

                <div class="bg-blue-600 text-white px-4 py-2 rounded mt-6 mb-3 font-semibold">

                    {{ $item->section }}

                </div>

            @endif

            <div class="bg-white shadow rounded mb-2 p-4">

                <div class="grid grid-cols-12 gap-3 items-center">

                    <div class="col-span-6">

                        {{ $item->checklist_item }}

                        <input
                            type="hidden"
                            name="checklists[{{ $i }}][machine_checklist_id]"
                            value="{{ $item->id }}">

                    </div>

                    <div class="col-span-2">

                        <select
                            name="checklists[{{ $i }}][result]"
                            class="w-full border rounded p-2"
                            required>

                            <option value="">Select</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                            <option value="N/A">N/A</option>

                        </select>

                    </div>

                    <div class="col-span-4">

                        <input
                            type="text"
                            name="checklists[{{ $i }}][remarks]"
                            class="w-full border rounded p-2"
                            placeholder="Remarks">

                    </div>

                </div>

            </div>

        @endforeach

        <div class="mt-8 flex justify-end">

            <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">

                Save Checklist

            </button>

        </div>

    </form>

</div>

@endsection