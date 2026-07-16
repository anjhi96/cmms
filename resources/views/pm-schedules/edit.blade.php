@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">
        Fill PM Record
    </h1>

    <form method="POST" action="{{ route('pm-schedules.update', $pmSchedule) }}" class="bg-white p-6 rounded shadow">

        @csrf
        @method('PUT')

        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl p-6 shadow mb-6">

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
                        {{ $pmSchedule->last_pm_date ?? '-' }}
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

                    <input required name="order_number" type="text" class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <label name="actual_date" class="block mb-2 text-sm font-medium">
                        Actual Date
                    </label>

                    <input required name="actual_date" type="date"
                        value="{{ old('actual_date', $pmSchedule->actual_date ?? date('Y-m-d')) }}"
                        class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <label name="pic" class="block mb-2 text-sm font-medium">
                        PIC PM
                    </label>

                    <input required name="pic" type="text" readonly
                        value="{{ old('pic', $pmSchedule->pic ?? auth()->user()->name) }}"
                        class="w-full bg-gray-100 border rounded-lg p-3">
                </div>

                <div>
                    <label name="start_time" class="block mb-2 text-sm font-medium">
                        Start PM
                    </label>

                    <input required name="start_time" type="time" id="start_time"
                        value="{{ old('start_time', $pmSchedule->start_time) }}" class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <label name="end_time" class="block mb-2 text-sm font-medium">
                        End PM
                    </label>

                    <input required name="end_time" type="time" id="end_time"
                        value="{{ old('end_time', $pmSchedule->end_time) }}" class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <label name="duration" class="block mb-2 text-sm font-medium">
                        Duration (Hours)
                    </label>

                    <input name="duration" type="text" id="duration" readonly value="{{ $pmSchedule->duration }}"
                        class="w-full border rounded-lg p-3 bg-gray-100">

                    <p id="duration_error" class="text-red-500 text-sm mt-1 hidden">
                        End time cannot be earlier than start time
                    </p>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

                {{-- Tampilkan hanya untuk type tertentu --}}
                @if (in_array($pmSchedule->machine_type, ['NDE2003', 'NDB']))
                    <div>
                        <label class="block mb-2 text-sm font-medium">
                            Oil Change
                        </label>

                        <select required name="oil_change" class="w-full border rounded-lg p-3">
                            <option value="">-- Select --</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>

                        </select>
                    </div>
                @endif

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        Greasing
                    </label>

                    <select required name="greasing" class="w-full border rounded-lg p-3">

                        <option value="">-- Select --</option>
                        <option value="YES">YES</option>
                        <option value="NO">NO</option>

                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        WO ZSBP
                    </label>

                    <select required name="wo_zsbp" class="w-full border rounded-lg p-3">
                        <option value="">-- Select --</option>
                        <option value="YES">YES</option>
                        <option value="NO">NO</option>

                    </select>
                </div>

            </div>
            <div class="gap-3 mt-5">
                <label class="block mb-2 text-sm font-medium">
                    Remarks
                </label>

                <textarea required name="remarks" rows="3" class="w-full border rounded-lg p-3"
                    placeholder="Contoh: Kapstan 1,2 oblak, kapstan 3 bocor, ganti ring kapstan 1 dan 4, ganti nylon, ganti spindel, ganti countersheave, gant belt 1200, 1800, 1500"></textarea>
            </div>

        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-6">

            <h3 class="text-lg font-semibold mb-6">
                PM Result
            </h3>
            <div id="problem-wrapper">

                {{-- Problem Section --}}
                <div class="problem-row flex gap-2 mb-2">

                    {{-- Problem --}}
                    <select required name="problems[0][problem]" class="problem-select border p-2 w-1/2 rounded">
                        <option value="">-- Select Problem --</option>
                        @foreach ($bigProblems as $problem)
                            <option value="{{ $problem->id }}" data-category="{{ strtolower(trim($problem->category)) }}">

                                {{ $problem->problem }}

                            </option>
                        @endforeach

                    </select>

                    {{-- Finding --}}
                    <select required name="problems[0][finding]" class="finding-select border p-2 flex-1 rounded">

                        <option value="">-- Finding --</option>

                    </select>

                    {{-- Severity --}}
                    <select required name="problems[0][severity]" class="border p-2 rounded w-1/5">
                        <option value="">-- Severity --</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>

                    </select>

                    <button type="button" onclick="removeProblem(this)" class="bg-red-500 text-white px-3 rounded">

                        X

                    </button>

                </div>

            </div>

            <button type="button" onclick="addProblem()" class="mt-2 bg-green-600 text-white px-3 py-1 rounded">

                + Add Problem

            </button>



        </div>



        {{-- PM Measurement --}}

        <div class="bg-white rounded-lg shadow p-6 mt-6">

            <h2 class="text-lg font-semibold mb-4">
                Measurement
            </h2>

            <div class="overflow-x-auto">

                <table class="min-w-full border">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="border px-3 py-2 text-left">
                                Measurement Item
                            </th>

                            <th class="border px-3 py-2 text-center">
                                Standard
                            </th>

                            <th class="border px-3 py-2 text-center">
                                Actual
                            </th>

                            <th class="border px-3 py-2 text-center">
                                Unit
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($measurements as $i => $measurement)
                            <tr>

                                <td class="border px-3 py-2">

                                    {{ $measurement->measurement_item }}

                                    <input type="hidden"
                                        name="measurements[{{ $i }}][machine_measurement_id]"
                                        value="{{ $measurement->id }}">

                                    <input type="hidden" name="measurements[{{ $i }}][measurement_item]"
                                        value="{{ $measurement->measurement_item }}">

                                </td>

                                <td class="border px-3 py-2 text-center">

                                    {{ $measurement->standard ?? '-' }}

                                    <input type="hidden" name="measurements[{{ $i }}][standard]"
                                        value="{{ $measurement->standard }}">

                                </td>

                                <td class="border px-3 py-2">

                                    <input required type="text"
                                        name="measurements[{{ $i }}][measurement_value]"
                                        class="w-full border rounded px-2 py-1">

                                </td>

                                <td class="border px-3 py-2 text-center">

                                    {{ $measurement->unit }}

                                    <input type="hidden" name="measurements[{{ $i }}][unit]"
                                        value="{{ $measurement->unit }}">

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="border text-center py-5 text-gray-500">

                                    No Measurement Found

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Sparepart Usage --}}

        <div class="mt-6 bg-white p-4 rounded shadow">

            <h2 class="text-lg font-bold mb-4">
                Sparepart Usage
            </h2>

            <div id="sparepart-wrapper">

                <div class="sparepart-row flex gap-2 mb-2">

                    <select required name="spareparts[0][sparepart_id]" placeholder="Select Sparepart"
                        class="sparepart-select border p-2 w-2/3 rounded">
                    </select>

                    <input required type="number" name="spareparts[0][qty]" placeholder="Qty"
                        class="border p-2 w-1/3 rounded">

                    <button type="button" onclick="removeSparepart(this)" class="bg-red-500 text-white px-3 rounded">

                        X

                    </button>

                </div>

            </div>

            <button type="button" onclick="addSparepart()" class="mt-2 bg-green-600 text-white px-3 py-1 rounded">

                + Add Sparepart

            </button>

        </div>

        <div class="flex justify-end">

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">

                Lanjut Isi Checklist

            </button>

        </div>

        {{-- Problem  --}}
        <script>
            let problemIndex = 1;

            function addProblem() {

                let html = `
        <div class="problem-row flex gap-2 mb-2">

            <select
                name="problems[${problemIndex}][problem]"
                class="problem-select border p-2 w-1/2 rounded">

                <option value="">-- Select Problem --</option>
                        @foreach ($bigProblems as $problem)
                            <option value="{{ $problem->id }}" data-category="{{ strtolower(trim($problem->category)) }}">

                                {{ $problem->problem }}

                            </option>
                        @endforeach
            </select>

            <select
                name="problems[${problemIndex}][finding]"
                class="finding-select border p-2 flex-1 rounded">

                <option value="">-- Finding --</option>

            </select>

            <select
                name="problems[${problemIndex}][severity]"
                class="border p-2 rounded w-1/5">

                <option value="">-- Severity --</option>

                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>

            </select>

            <button type="button" onclick="removeProblem(this)" class="bg-red-500 text-white px-3 rounded">

                X

            </button>

        </div>
    `;

    // console.log(html);
                document
                    .getElementById('problem-wrapper')
                    .insertAdjacentHTML('beforeend', html);

                problemIndex++;
            }

            function removeProblem(button) {

                const rows = document.querySelectorAll('.problem-row');

                if (rows.length > 1) {
                    button.parentElement.remove();
                }

            }
        </script>

        {{-- Duration Calculation --}}
        <script>
            const startInput = document.getElementById('start_time');
            const endInput = document.getElementById('end_time');
            const durationInput = document.getElementById('duration');
            const errorText = document.getElementById('duration_error');

            function calculateDuration() {

                if (!startInput.value || !endInput.value) return;

                let start = startInput.value.split(':');
                let end = endInput.value.split(':');

                let startMinutes = parseInt(start[0]) * 60 + parseInt(start[1]);
                let endMinutes = parseInt(end[0]) * 60 + parseInt(end[1]);

                // ❌ VALIDASI ERROR
                if (endMinutes < startMinutes) {
                    durationInput.value = '';
                    errorText.classList.remove('hidden');
                    endInput.classList.add('border-red-500');
                    return;
                }

                // ✅ CLEAR ERROR
                errorText.classList.add('hidden');
                endInput.classList.remove('border-red-500');

                let diff = endMinutes - startMinutes;

                let hours = Math.floor(diff / 60);
                let minutes = diff % 60;

                durationInput.value = `${hours} Hours ${minutes} Minutes`;
            }

            startInput.addEventListener('change', calculateDuration);
            endInput.addEventListener('change', calculateDuration);
        </script>

        {{-- Problem Findings --}}
        <script>
            const findings = @json($problemFindings);

            document.addEventListener('change', function(e) {

                if (!e.target.classList.contains('problem-select'))
                    return;

                const category = e.target.selectedOptions[0]
                    .dataset.category
                    .trim()
                    .toLowerCase();

                const findingSelect = e.target
                    .closest('.problem-row')
                    .querySelector('.finding-select');

                findingSelect.innerHTML =
                    '<option value="">-- Finding --</option>';

                if (findings[category]) {

                    findings[category].forEach(function(item) {

                        findingSelect.innerHTML += `

                <option value="${item.id}">

                    ${item.finding}

                </option>

            `;

                    });

                }

            });
        </script>

        <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

        <script>
            const spareparts = @json($spareparts);
        </script>

        <script>
            let sparepartIndex = 1;

            function initTomSelect(element) {

                if (element.tomselect) {
                    element.tomselect.destroy();
                }

                new TomSelect(element, {

                    valueField: 'id',

                    labelField: 'material_number',

                    searchField: [
                        'location',
                        'material_number',
                        'description',
                        'remarks'
                    ],

                    options: spareparts,

                    create: false,

                    maxOptions: 100,

                    render: {

                        option: function(item, escape) {

                            return `

                <div style="padding:8px">

                    <div style="font-size:12px;color:#6b7280">

                        📍 ${escape(item.location ?? '-')}

                    </div>

                    <div style="font-weight:700">

                        ${escape(item.material_number)}

                    </div>

                    <div>

                        ${escape(item.description)}

                    </div>

                    <div style="font-size:12px;color:#6b7280">

                        ${escape(item.remarks ?? '-')}

                    </div>

                </div>

                `;

                        },

                        item: function(item, escape) {

                            return `

                <div>

                    ${escape(item.material_number)}
                    -
                    ${escape(item.description)}

                </div>

                `;

                        }

                    }

                });

            }

            document.addEventListener('DOMContentLoaded', function() {

                document.querySelectorAll('.sparepart-select').forEach(function(el) {

                    initTomSelect(el);

                });

            });

            function addSparepart() {

                let html = `

                <div class="sparepart-row flex gap-2 mb-2">

                    <select
                        name="spareparts[${sparepartIndex}][sparepart_id]"
                        placeholder="Select Sparepart" class="sparepart-select border p-2 w-2/3 rounded">
                    </select>

                    <input
                        type="number"
                        name="spareparts[${sparepartIndex}][qty]"
                        placeholder="Qty"
                        class="border p-3 w-1/3 rounded">

                    <button
                        type="button"
                        onclick="removeSparepart(this)"
                        class="bg-red-500 text-white px-3 rounded">

                        X

                    </button>

                </div>

                `;

                document
                    .getElementById('sparepart-wrapper')
                    .insertAdjacentHTML('beforeend', html);

                const selects = document.querySelectorAll('.sparepart-select');

                initTomSelect(selects[selects.length - 1]);

                sparepartIndex++;

            }

            function removeSparepart(button) {

                button.closest('.sparepart-row').remove();

            }
        </script>
    @endsection
