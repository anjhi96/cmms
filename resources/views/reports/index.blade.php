@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    CMMS Reports Dashboard
</h1>

<div class="grid grid-cols-3 gap-4">

    {{-- MACHINE REPORT --}}
    <div class="bg-white p-6 rounded shadow">

        <h2 class="text-lg font-bold mb-2">
            Machine Overview
        </h2>

        <p class="text-gray-600">
            Total Machines: 120
        </p>

        <p class="text-gray-600">
            Active: 110
        </p>

        <p class="text-gray-600">
            Inactive: 10
        </p>

    </div>

    {{-- PM REPORT --}}
    <div class="bg-white p-6 rounded shadow">

        <h2 class="text-lg font-bold mb-2">
            PM Execution
        </h2>

        <p class="text-gray-600">
            Completed PM: 85
        </p>

        <p class="text-gray-600">
            Pending PM: 20
        </p>

    </div>

    {{-- SPAREPART REPORT --}}
    <div class="bg-white p-6 rounded shadow">

        <h2 class="text-lg font-bold mb-2">
            Sparepart Usage
        </h2>

        <p class="text-gray-600">
            Total Sparepart: 10,000+
        </p>

        <p class="text-gray-600">
            Most Used: Bearing 6205
        </p>

    </div>

</div>

@endsection