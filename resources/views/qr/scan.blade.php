@extends('layouts.guest')

@section('content')

<script src="https://unpkg.com/html5-qrcode"></script>

<div class="min-h-screen bg-slate-100">

    <div class="max-w-5xl mx-auto py-16 px-6">

        <div class="text-center">

            <h1 class="text-4xl font-bold">

                Scan QR Mesin

            </h1>

            <p class="mt-4 text-slate-500">

                Arahkan kamera ke QR Code yang terdapat pada mesin.

            </p>

        </div>

        <div class="mt-12">

            <div
                id="reader"
                class="mx-auto rounded-3xl overflow-hidden shadow-xl bg-black max-w-xl h-[450px]">

            </div>

        </div>

        <div class="mt-10 text-center">

            <button
                id="startScanner"
                class="rounded-xl bg-blue-600 px-8 py-4 text-white font-semibold">

                📷 Aktifkan Kamera

            </button>

        </div>

    </div>

</div>

@endsection