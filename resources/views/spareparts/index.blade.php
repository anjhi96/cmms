@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <div>
        <h1 class="text-2xl font-bold">
            Sparepart Master
        </h1>

        <p class="text-gray-500">
            List of spareparts (SAP reference)
        </p>
    </div>

    <a href="{{ route('spareparts.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded">

        + Add Sparepart

    </a>

</div>

<div class="bg-white rounded shadow overflow-hidden">

<table class="w-full">

    <thead class="bg-gray-100">

        <tr>

            <th class="p-3 text-left">SAP Number</th>
            <th class="p-3 text-left">Description</th>
            <th class="p-3 text-left">Location</th>
            <th class="p-3 text-center">Action</th>

        </tr>

    </thead>

    <tbody>

        <tr class="border-t">

            <td class="p-3">SP-001</td>
            <td class="p-3">Bearing 6205</td>
            <td class="p-3">Warehouse A</td>

            <td class="p-3 text-center">
                <button class="bg-yellow-500 text-white px-3 py-1 rounded">
                    Edit
                </button>

                <button class="bg-red-600 text-white px-3 py-1 rounded">
                    Delete
                </button>
            </td>

        </tr>

    </tbody>

</table>

</div>

@endsection