@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">Create PM Schedule</h1>

<form method="POST"
    action="{{ route('pm-schedules.store') }}"
    class="bg-white p-6 rounded shadow">

    @csrf

    <div class="mb-4">
        <label>Machine</label>

        <select name="machine_id"
            class="w-full border p-2 rounded">

            @foreach($machines as $machine)

            <option value="{{ $machine->id }}">
                {{ $machine->machine_number }}
                - {{ $machine->machine_type }}
            </option>

            @endforeach

        </select>
    </div>

    <div class="mb-4">
        <label>Order Number</label>
        <input type="text"
            name="order_number"
            class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Plan Month</label>
        <input type="text"
            name="plan_month"
            class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Plan Year</label>
        <input type="number"
            name="plan_year"
            value="{{ date('Y') }}"
            class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>Due Date</label>
        <input type="date"
            name="due_date"
            class="w-full border p-2 rounded">
    </div>

    <div class="mb-4">
        <label>PIC</label>
        <input type="text"
            name="pic"
            class="w-full border p-2 rounded">
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Save
    </button>

</form>

@endsection