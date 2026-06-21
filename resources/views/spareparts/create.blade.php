@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Add Sparepart
</h1>

<div class="bg-white p-6 rounded shadow">

<form>

    {{-- SAP Number --}}
    <div class="mb-4">
        <label class="block mb-1">SAP Number</label>
        <input type="text"
               class="w-full border p-2 rounded"
               placeholder="SAP-0001">
    </div>

    {{-- Description --}}
    <div class="mb-4">
        <label class="block mb-1">Description</label>
        <input type="text"
               class="w-full border p-2 rounded"
               placeholder="Bearing / Seal / Motor Part">
    </div>

    {{-- Location --}}
    <div class="mb-4">
        <label class="block mb-1">Location</label>
        <input type="text"
               class="w-full border p-2 rounded"
               placeholder="Warehouse A / B / C">
    </div>

    <div class="flex gap-2">

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Save
        </button>

        <a href="{{ route('spareparts.index') }}"
           class="bg-gray-400 text-white px-4 py-2 rounded">
            Cancel
        </a>

    </div>

</form>

</div>

@endsection