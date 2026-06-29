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
                    <label class="block mb-2 text-sm font-medium">
                        Order Number
                    </label>

                    <input type="text" name="order_number" class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        Actual Date
                    </label>

                    <input type="date" name="actual_date" value="{{ date('Y-m-d') }}"
                        class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        PIC PM
                    </label>

                    <input type="text" readonly value="{{ auth()->user()->name }}"
                        class="w-full bg-gray-100 border rounded-lg p-3">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        Start PM
                    </label>

                    <input type="time" id="start_time" name="start_time" class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        End PM
                    </label>

                    <input type="time" id="end_time" name="end_time" class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        Duration (Hours)
                    </label>

                    <input type="text" id="duration" readonly class="w-full border rounded-lg p-3 bg-gray-100">

                    <p id="duration_error" class="text-red-500 text-sm mt-1 hidden">
                        End time cannot be earlier than start time
                    </p>
                </div>

            </div>

        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-6">

            <h3 class="text-lg font-semibold mb-6">
                PM Result
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Big Problem
                    </label>

                    <select name="big_problem" class="w-full border rounded-lg p-3">

                        <option value="">
                            -- No Big Problem --
                        </option>

                        @foreach ($bigProblems as $problem)
                            <option value="{{ $problem->problem }}"
                                {{ old('big_problem', $pmSchedule->big_problem) == $problem->problem ? 'selected' : '' }}>
                                {{ $problem->problem }}
                            </option>
                        @endforeach

                    </select>
                    @if ($bigProblems->isEmpty())
                        <p class="text-sm text-gray-500 mt-2">
                            No big problem registered for this machine type.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        Remarks
                    </label>

                    <textarea name="remarks" rows="4" class="w-full border rounded-lg p-3"></textarea>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

                {{-- Tampilkan hanya untuk type tertentu --}}
                @if (in_array($pmSchedule->machine_type, ['NDE2003', 'NDB']))
                    <div>
                        <label class="block mb-2 text-sm font-medium">
                            Oil Change
                        </label>

                        <select name="oil_change" class="w-full border rounded-lg p-3">

                            <option value="">Select</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>

                        </select>
                    </div>
                @endif

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        Greasing
                    </label>

                    <select name="greasing" class="w-full border rounded-lg p-3">

                        <option value="YES">YES</option>
                        <option value="NO">NO</option>

                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">
                        WO ZSBP
                    </label>

                    <select name="wo_zsbp" class="w-full border rounded-lg p-3">

                        <option value="YES">YES</option>
                        <option value="NO">NO</option>

                    </select>
                </div>

            </div>

        </div>

        <div class="flex flex-col md:flex-row gap-3 justify-end">

            <button type="submit" name="status" value="IN PROGRESS"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg">

                Save Draft

            </button>

            <button type="submit" name="status" value="DONE"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">

                Complete PM

            </button>

        </div>

        {{-- PM Measurement --}}
        <div class="mt-6 bg-white p-4 rounded shadow">

            <h2 class="text-lg font-bold mb-4">PM Measurement</h2>

            <div id="pm-details-wrapper">

                <!-- row default -->
                <div class="pm-row flex gap-2 mb-2">

                    <input type="text" name="details[0][item]" placeholder="Measurement Item"
                        class="border p-2 w-1/3 rounded">

                    <input type="text" name="details[0][value]" placeholder="Value" class="border p-2 w-1/3 rounded">

                    <input type="text" name="details[0][unit]" placeholder="Unit" class="border p-2 w-1/6 rounded">

                    <button type="button" onclick="removeRow(this)" class="bg-red-500 text-white px-2 rounded">
                        X
                    </button>

                </div>

            </div>

            <button type="button" onclick="addRow()" class="mt-2 bg-blue-600 text-white px-3 py-1 rounded">

                + Add Measurement

            </button>

        </div>

        <div class="mt-6 bg-white p-4 rounded shadow">

            <h2 class="text-lg font-bold mb-4">Sparepart Usage</h2>

            <div id="sparepart-wrapper">

                <!-- row default -->
                <div class="sparepart-row flex gap-2 mb-2">

                    <input type="text" name="spareparts[0][name]" placeholder="Sparepart Name"
                        class="border p-2 w-1/2 rounded">

                    <input type="number" name="spareparts[0][qty]" placeholder="Qty" class="border p-2 w-1/4 rounded">

                    <button type="button" onclick="removeSparepart(this)" class="bg-red-500 text-white px-2 rounded">
                        X
                    </button>

                </div>

            </div>

            <button type="button" onclick="addSparepart()" class="mt-2 bg-green-600 text-white px-3 py-1 rounded">

                + Add Sparepart
            </button>

        </div>

        {{-- PM Details --}}
        <script>
            let index = 1;

            function addRow() {

                const wrapper = document.getElementById('pm-details-wrapper');

                const row = document.createElement('div');
                row.classList.add('pm-row', 'flex', 'gap-2', 'mb-2');

                row.innerHTML = `
        <input type="text"
            name="details[${index}][item]"
            placeholder="Measurement Item"
            class="border p-2 w-1/3 rounded">

        <input type="text"
            name="details[${index}][value]"
            placeholder="Value"
            class="border p-2 w-1/3 rounded">

        <input type="text"
            name="details[${index}][unit]"
            placeholder="Unit"
            class="border p-2 w-1/6 rounded">

        <button type="button"
            onclick="removeRow(this)"
            class="bg-red-500 text-white px-2 rounded">
            X
        </button>
    `;

                wrapper.appendChild(row);

                index++;
            }

            function removeRow(button) {
                button.parentElement.remove();
            }
        </script>

        {{-- Sparepart Usage --}}
        <script>
            let spareIndex = 1;

            function addSparepart() {

                const wrapper = document.getElementById('sparepart-wrapper');

                const row = document.createElement('div');
                row.classList.add('sparepart-row', 'flex', 'gap-2', 'mb-2');

                row.innerHTML = `
        <input type="text"
            name="spareparts[${spareIndex}][name]"
            placeholder="Sparepart Name"
            class="border p-2 w-1/2 rounded">

        <input type="number"
            name="spareparts[${spareIndex}][qty]"
            placeholder="Qty"
            class="border p-2 w-1/4 rounded">

        <button type="button"
            onclick="removeSparepart(this)"
            class="bg-red-500 text-white px-2 rounded">
            X
        </button>
    `;

                wrapper.appendChild(row);

                spareIndex++;
            }

            function removeSparepart(button) {
                button.parentElement.remove();
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

        {{-- Machine Problem Dropdown --}}
        <script>
            document.getElementById('machine_type').addEventListener('change', function() {

                let type = this.value;
                let problemSelect = document.getElementById('big_problem');

                // reset dropdown
                problemSelect.innerHTML = '<option value="">Loading...</option>';

                if (!type) {
                    problemSelect.innerHTML = '<option value="">Select Problem</option>';
                    return;
                }

                fetch(`/machine-problems/by-type/${type}`)
                    .then(res => res.json())
                    .then(data => {

                        problemSelect.innerHTML = '<option value="">Select Problem</option>';

                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.problem;
                            option.text = item.problem;
                            problemSelect.appendChild(option);
                        });

                    })
                    .catch(err => {
                        console.error(err);
                        problemSelect.innerHTML = '<option>Error loading data</option>';
                    });

            });
        </script>
    @endsection
