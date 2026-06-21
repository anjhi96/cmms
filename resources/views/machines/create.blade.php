@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">Add Machine</h1>

<div class="bg-white p-6 rounded shadow">

<form method="POST" action="{{ route('machines.store') }}">
    @csrf

    {{-- AREA --}}
    <div class="mb-4">
        <label>Area</label>
        <select name="area" class="w-full border p-2 rounded">
            <option>KA-WWD</option>
            <option>KA-C&B</option>
        </select>
    </div>

    {{-- MACHINE TYPE --}}
    <div class="mb-4">
        <label>Machine Type</label>
        <input type="text"
               name="machine_type"
               class="w-full border p-2 rounded"
               placeholder="NDE / NDB / SHX / REWINDER">
    </div>

    {{-- MACHINE NUMBER --}}
    <div class="mb-4">
        <label>Machine Number</label>
        <input type="text"
               name="machine_number"
               class="w-full border p-2 rounded"
               placeholder="MTR-001">
    </div>

    {{-- DESCRIPTION --}}
    <div class="mb-4">
        <label>Description</label>
        <input type="text"
               name="description"
               class="w-full border p-2 rounded">
    </div>

    {{-- STATUS --}}
    <div class="mb-4">
        <label>Status</label>
        <select name="status" class="w-full border p-2 rounded">
            <option value="ACTIVE">ACTIVE</option>
            <option value="INACTIVE">INACTIVE</option>
        </select>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Save
    </button>

</form>

</div>

@endsection