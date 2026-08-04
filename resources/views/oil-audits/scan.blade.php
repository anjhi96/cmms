@extends('layouts.app')

@section('title', 'Audit Oli')

@section('content')
    <div class="mx-auto max-w-3xl" x-data="{ manualOpen: false }">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 0 0-2.828 0l-1.172 1.172a2 2 0 0 1-2.828 0l-1.172-1.172a2 2 0 0 0-2.828 0l-3.6 3.6" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v2M3 5h2M19 3v2M17 5h2M5 19v2M3 19h2M19 19v2M17 19h2" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10v10H7z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">Audit Oli Cepat</h1>
            <p class="mt-2 text-sm text-slate-500 sm:text-base">Scan QR pada mesin, pilih kondisi oli, lalu lanjut ke mesin berikutnya.</p>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-950 shadow-xl">
            <div id="oil-audit-reader" class="min-h-[320px] bg-slate-950 sm:min-h-[420px]"></div>
            <div class="border-t border-slate-800 bg-slate-900 p-4 text-center">
                <p id="scanner-status" class="mb-3 text-sm text-slate-300">Arahkan kamera ke QR code nomor mesin.</p>
                <button id="start-scanner" type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 focus:ring-offset-slate-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553 2.276A2 2 0 0 1 20.658 14l-4.553 2.276A2 2 0 0 1 13 14.484v-4.968A2 2 0 0 1 15 10z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h.01M3 12h.01M3 18h.01M21 6h.01M21 12h.01M21 18h.01" />
                    </svg>
                    Aktifkan Kamera
                </button>
            </div>
        </div>

        <div class="mt-5 text-center">
            <button type="button" @click="manualOpen = !manualOpen" class="text-sm font-medium text-sky-700 hover:text-sky-900">
                QR tidak terbaca? Masukkan nomor mesin secara manual
            </button>
            <form x-show="manualOpen" x-cloak method="GET" action="{{ route('oil-audits.entry', ['machineNumber' => '__machine__']) }}" class="mx-auto mt-4 flex max-w-md gap-2" id="manual-machine-form">
                <input id="manual-machine-number" type="text" required autocomplete="off" placeholder="Contoh: MSN-001" class="min-w-0 flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm uppercase outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                <button class="rounded-xl bg-slate-800 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-700">Lanjut</button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startButton = document.getElementById('start-scanner');
            const status = document.getElementById('scanner-status');
            const manualForm = document.getElementById('manual-machine-form');
            const entryUrl = @json(route('oil-audits.entry', ['machineNumber' => '__machine__']));
            let scanner;
            let isScanning = false;

            const openMachine = (machineNumber) => {
                const value = machineNumber.trim();

                if (!value) {
                    status.textContent = 'Nomor mesin pada QR tidak ditemukan.';
                    return;
                }

                window.location.assign(entryUrl.replace('__machine__', encodeURIComponent(value)));
            };

            const startScanner = async () => {
                if (isScanning || typeof Html5Qrcode === 'undefined') {
                    return;
                }

                scanner = new Html5Qrcode('oil-audit-reader');
                isScanning = true;
                startButton.disabled = true;
                startButton.classList.add('cursor-not-allowed', 'opacity-60');
                status.textContent = 'Kamera aktif. Posisikan QR code di dalam area pemindaian.';

                try {
                    await scanner.start(
                        { facingMode: 'environment' },
                        { fps: 12, qrbox: { width: 250, height: 250 } },
                        async (decodedText) => {
                            if (!isScanning) return;

                            isScanning = false;
                            status.textContent = 'QR terbaca. Membuka data mesin...';
                            await scanner.stop();
                            openMachine(decodedText);
                        },
                        () => {}
                    );
                } catch (error) {
                    isScanning = false;
                    startButton.disabled = false;
                    startButton.classList.remove('cursor-not-allowed', 'opacity-60');
                    status.textContent = 'Kamera belum dapat digunakan. Periksa izin kamera atau gunakan input manual.';
                }
            };

            startButton.addEventListener('click', startScanner);

            manualForm.addEventListener('submit', (event) => {
                event.preventDefault();
                openMachine(document.getElementById('manual-machine-number').value);
            });

            @if (session('success'))
                window.setTimeout(startScanner, 250);
            @endif
        });
    </script>
@endsection
