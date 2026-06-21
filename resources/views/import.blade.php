@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Import Machine
</h1>

<div class="bg-white p-6 rounded shadow">

    <form method="POST"
          action="{{ route('machines.import') }}"
          enctype="multipart/form-data">

        @csrf

        <input type="file"
               name="file"
               class="border p-2 w-full">

        <button
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">

            Import

        </button>

    </form>

</div>

@endsection