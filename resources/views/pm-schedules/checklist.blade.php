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
                    {{ $pmSchedule->actual_date ? \Carbon\Carbon::parse($pmSchedule->actual_date)->format('d-m-Y') : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    Duration
                </label>

                <div class="font-semibold">
                    {{ $pmSchedule->duration_formatted }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    Cycle PM
                </label>

                <div class="font-semibold">
                    {{ $pmSchedule->machine->pm_cycle_value }}
                    {{ ucfirst($pmSchedule->machine->pm_cycle_unit) }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    Next PM
                </label>

                <div class="font-semibold">
                    {{ $nextPm ? $nextPm->format('d-m-Y') : '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">
                    Sparepart Cost
                </label>

                <div class="font-semibold
                        @if($spareCost == 0)
                            text-gray-500
                        @elseif($spareCost < 1000)
                            text-green-600
                        @elseif($spareCost < 3000)
                            text-yellow-600
                        @else
                            text-red-600
                        @endif
                    ">
                    $ {{ number_format($spareCost, 2, ',', '.') }}
                </div>
            </div>

        </div>

    </div>

    {{-- Checklist --}}
    <form action="{{ route('pm-schedules.checklist.save', $pmSchedule) }}" method="POST">

        @csrf

        <div class="overflow-auto max-h-[75vh] bg-white rounded-lg shadow">

            <table class="min-w-full border-collapse">

                <thead class="sticky top-0 z-20 bg-gray-100 shadow-sm">

                    <tr>

                        <th class="border px-3 py-3 text-left w-5/12 bg-gray-100">
                            Checklist Item
                        </th>

                        <th title="Clean" class="border px-3 py-3 text-center w-24">

                            <span class="hidden xl:inline">
                                Clean
                            </span>

                            <span class="inline xl:hidden">
                                CLN
                            </span>

                        </th>

                        <th title="Check" class="border px-3 py-3 text-center w-24">

                            <span class="hidden xl:inline">
                                Check
                            </span>

                            <span class="inline xl:hidden">
                                CHK
                            </span>

                        </th>

                        <th title="Lubrication" class="border px-3 py-3 text-center w-28">

                            <span class="hidden xl:inline">
                                Lubrication
                            </span>

                            <span class="inline xl:hidden">
                                LUB
                            </span>

                        </th>

                        <th title="Replace" class="border px-3 py-3 text-center w-24">

                            <span class="hidden xl:inline">
                                Replace
                            </span>

                            <span class="inline xl:hidden">
                                REP
                            </span>

                        </th>


                        <th title="Remarks" class="border px-3 py-3 text-left bg-gray-100">
                            <span class="hidden xl:inline">
                                Remarks
                            </span>

                            <span class="inline xl:hidden">
                                Rem..
                            </span>
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @php
                    $currentSection = '';
                    @endphp

                    @foreach ($checklists as $i => $item)
                    @php
                    $oldChecklist = $pmChecklists[$item->id] ?? null;
                    @endphp

                    @if ($currentSection != $item->section)
                    @php
                    $currentSection = $item->section;
                    @endphp


                    <tr>

                        <td colspan="6" class="bg-slate-200 text-slate-800 font-semibold px-4 py-2 border">

                            {{ $item->section }}

                        </td>

                    </tr>
                    @endif

                    <tr class="hover:bg-gray-50">

                        <!-- Checklist item -->
                        <td class="border px-3 py-3">

                            <div class="flex items-center gap-2">

                                <span>{{ $item->checklist_item }}</span>

                                @if($item->maintenance_type == 'replace')

                                <span class="text-[10px] font-bold px-2 py-0.5 rounded
                bg-orange-100 text-orange-700">

                                    REP

                                </span>

                                @endif

                            </div>

                            <input type="hidden" name="checklists[{{ $i }}][machine_checklist_id]"
                                value="{{ $item->id }}">
                        </td>

                        {{-- CLEAN --}}
                        <td class="border text-center">

                            @if($item->maintenance_type == 'clean')

                            <input type="hidden" name="checklists[{{ $i }}][clean]" value="NO">

                            <input type="checkbox" name="checklists[{{ $i }}][clean]" value="YES"
                                {{ $oldChecklist?->clean == 'YES' ? 'checked' : '' }} class="
        h-6 w-6
        rounded
        border-2
        border-slate-400
        text-green-600
        focus:ring-2
        focus:ring-green-500
        cursor-pointer
        transition
        duration-150
    ">
                            @endif

                        </td>

                        {{-- CHECK --}}
                        <td class="border text-center">
                            @if($item->maintenance_type != 'replace')

                            <input type="hidden" name="checklists[{{ $i }}][check]" value="NO">

                            <input type="checkbox" name="checklists[{{ $i }}][check]" value="YES"
                                {{ $oldChecklist?->check == 'YES' ? 'checked' : '' }} class="
        h-6 w-6
        rounded
        border-2
        border-slate-400
        text-green-600
        focus:ring-2
        focus:ring-green-500
        cursor-pointer
        transition
        duration-150
    ">

                            @endif

                        </td>

                        {{-- LUBRICATION --}}
                        <td class="border text-center">

                            @if($item->maintenance_type == 'lubrication')

                            <input type="hidden" name="checklists[{{ $i }}][lubrication]" value="NO">

                            <input type="checkbox" name="checklists[{{ $i }}][lubrication]" value="YES"
                                {{ $oldChecklist?->lubrication == 'YES' ? 'checked' : '' }} class="
        h-6 w-6
        rounded
        border-2
        border-slate-400
        text-green-600
        focus:ring-2
        focus:ring-green-500
        cursor-pointer
        transition
        duration-150
    ">

                            @endif

                        </td>

                        {{-- REPLACE --}}
                        <td class="border text-center">

                            <input type="hidden" name="checklists[{{ $i }}][replace]" value="NO">

                            <input type="checkbox" name="checklists[{{ $i }}][replace]" value="YES"
                                {{ $oldChecklist?->replace == 'YES' ? 'checked' : '' }} class="
        h-6 w-6
        rounded
        border-2
        {{ $item->maintenance_type == 'replace'
            ? 'border-2 border-red-600 bg-red-50 text-red-600 accent-red-600 ring-4 ring-red-500/30'
            : 'border-slate-400' }}
        border-slate-400
        text-green-600
        focus:ring-2
        focus:ring-green-500
        cursor-pointer
        transition
        duration-150
    ">

                        </td>



                        {{-- REMARKS --}}
                        <td class="border px-3 py-2 text-center">

                            <button type="button"
                                class="remark-btn {{ $oldChecklist?->remarks ? 'text-green-600' : 'text-gray-500' }} text-gray-500 hover:text-blue-600 text-xl"
                                data-index="{{ $i }}" data-item="{{ $item->checklist_item }}">

                                {{ $oldChecklist?->remarks ? '📝' : '✏️' }}

                            </button>

                            <input type="hidden" id="remark-{{ $i }}" name="checklists[{{ $i }}][remarks]"
                                value="{{ $oldChecklist?->remarks ?? '' }}">

                        </td>

                    </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="flex justify-between mt-6">



            {{-- Back --}}
            <a href="{{ route('pm-schedules.edit', $pmSchedule->id) }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">

                ← Back to Fill PM

            </a>

            {{-- Save --}}
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">

                Save Checklist

            </button>

        </div>

    </form>

    {{-- Remarks Modal --}}
    <div id="remarkModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">

            <h2 class="text-lg font-bold mb-4">
                Checklist Remarks
            </h2>

            <div class="mb-4">

                <label class="block text-sm text-gray-500 mb-1">
                    Checklist Item
                </label>

                <div id="remarkItem" class="font-semibold bg-gray-100 rounded p-3">
                </div>

            </div>

            <div>

                <label class="block text-sm text-gray-500 mb-1">
                    Remarks
                </label>

                <textarea id="remarkTextarea" rows="5" class="w-full border rounded-lg p-3"
                    placeholder="Input remarks..."></textarea>

            </div>

            <div class="flex justify-end gap-3 mt-6">

                <button type="button" id="cancelRemark" class="px-4 py-2 rounded border">

                    Cancel

                </button>

                <button type="button" id="saveRemark" class="px-4 py-2 rounded bg-blue-600 text-white">

                    Save

                </button>

            </div>

        </div>

    </div>

    <script>
    let currentRemarkIndex = null;

    const modal = document.getElementById('remarkModal');

    const textarea = document.getElementById('remarkTextarea');

    const itemLabel = document.getElementById('remarkItem');

    document.querySelectorAll('.remark-btn').forEach(btn => {

        btn.addEventListener('click', function() {

            currentRemarkIndex = this.dataset.index;

            itemLabel.innerText = this.dataset.item;

            textarea.value =
                document.getElementById(
                    'remark-' + currentRemarkIndex
                ).value;

            modal.classList.remove('hidden');

            modal.classList.add('flex');

        });

    });

    document.getElementById('cancelRemark')
        .addEventListener('click', function() {

            modal.classList.add('hidden');

            modal.classList.remove('flex');

        });

    document.getElementById('saveRemark')
        .addEventListener('click', function() {

            const input =
                document.getElementById(
                    'remark-' + currentRemarkIndex
                );

            input.value = textarea.value;

            // Ganti warna icon kalau sudah ada remarks
            const button =
                document.querySelector(
                    `.remark-btn[data-index="${currentRemarkIndex}"]`
                );

            if (textarea.value.trim() !== '') {

                button.classList.remove(
                    'text-gray-500'
                );

                button.classList.add(
                    'text-green-600'
                );

                button.innerHTML = '📝';

            } else {

                button.classList.remove(
                    'text-green-600'
                );

                button.classList.add(
                    'text-gray-500'
                );

                button.innerHTML = '✏️';

            }

            modal.classList.add('hidden');

            modal.classList.remove('flex');

        });

    // Klik area hitam untuk close
    modal.addEventListener('click', function(e) {

        if (e.target === modal) {

            modal.classList.add('hidden');

            modal.classList.remove('flex');

        }

    });
    </script>
    @endsection