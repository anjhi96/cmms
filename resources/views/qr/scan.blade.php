@extends('layouts.guest')

@section('content')

<script src="https://unpkg.com/html5-qrcode"></script>

<div class="min-h-screen bg-slate-100">

    <div class="max-w-5xl mx-auto py-16 px-6">

        <div class="mb-8">

            <a
                href="{{ route('home') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 111.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>

                Kembali ke Beranda

            </a>

        </div>

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

<script>
document.getElementById('startScanner').addEventListener('click', function () {

    const scanner = new Html5Qrcode("reader");

    scanner.start(
        {
            facingMode: "environment"
        },
        {
            fps: 10,
            qrbox: 250
        },
        function(decodedText){

            scanner.stop();

            window.location.href = "/m/" + decodedText;

        },
        function(error){

            // abaikan error scanning

        }
    );

});
</script>

@endsection
