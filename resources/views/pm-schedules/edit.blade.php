@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">
        Fill PM Record
    </h1>

    <form method="POST" action="{{ route('pm-schedules.update', $pmSchedule) }}" class="bg-white p-6 rounded shadow">

        @csrf
        @method('PUT')

        <p><b>Order :</b> {{ $pmSchedule->order_number }}</p>
        <p><b>Machine :</b> {{ $pmSchedule->machine_number }}</p>

        <div class="mt-4">
            <label>Actual Date</label>
            <input type="date" name="actual_date" class="w-full border p-2 rounded">
        </div>

        <div class="mt-4">
            <label>Start PM</label>
            <input type="time" name="start_time" class="w-full border p-2 rounded">
        </div>

        <div class="mt-4">
            <label>End PM</label>
            <input type="time" name="end_time" class="w-full border p-2 rounded">
        </div>

        <div class="mt-4">
            <label>Big Problem</label>
            <textarea name="big_problem" class="w-full border p-2 rounded"></textarea>
        </div>

        <div class="mt-4">
            <label>Remarks</label>
            <textarea name="remarks" class="w-full border p-2 rounded"></textarea>
        </div>

        <div class="mt-4">
            <label>Next PM</label>
            <input type="date" name="next_pm" class="w-full border p-2 rounded">
        </div>

        <div class="mt-4">
            <label>Status</label>

            <select name="status" class="w-full border p-2 rounded">

                <option value="OPEN">OPEN</option>
                <option value="IN_PROGRESS">IN PROGRESS</option>
                <option value="DONE">DONE</option>

            </select>
        </div>

        <button class="mt-6 bg-green-600 text-white px-4 py-2 rounded">
            Save PM
        </button>

    </form>

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
@endsection
