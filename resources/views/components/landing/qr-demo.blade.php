<section id="scan-demo" class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- LEFT --}}
            <div>

                <span class="text-blue-600 font-semibold uppercase tracking-wider">

                    Machine History

                </span>

                <h2 class="mt-3 text-4xl font-bold text-slate-900">

                    Cukup Scan QR Code,
                    Semua Informasi Mesin Langsung Tersedia.

                </h2>

                <p class="mt-6 text-lg text-slate-600 leading-8">

                    Tidak perlu membuka dokumen atau mencari histori maintenance
                    secara manual. Scan QR yang terpasang pada mesin, lalu seluruh
                    informasi maintenance akan langsung ditampilkan.

                </p>

                <div class="mt-10 flex flex-col sm:flex-row gap-4">

                    <a href="{{ route('qr.scan') }}"
                        class="inline-flex justify-center items-center rounded-xl bg-blue-600 px-8 py-4 text-white font-semibold shadow-lg hover:bg-blue-700 transition">

                        📷 Scan Mesin

                    </a>

                    <div class="flex">

                        <input type="text" placeholder="Masukkan Nomor Mesin"
                            class="rounded-l-xl border border-slate-300 px-4 py-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">

                        <button class="rounded-r-xl bg-slate-800 text-white px-6 hover:bg-slate-900">

                            Cari

                        </button>

                    </div>

                </div>

                <div class="mt-8">

                    <p class="text-sm text-slate-500">

                        Contoh:

                    </p>

                    <div class="flex flex-wrap gap-3 mt-3">

                        <span class="px-4 py-2 rounded-full bg-slate-100 text-sm">

                            30001

                        </span>

                        <span class="px-4 py-2 rounded-full bg-slate-100 text-sm">

                            9004

                        </span>
                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div>

                <div class="rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">

                    {{-- Header --}}

                    <div class="bg-blue-600 text-white px-6 py-5">

                        <h3 class="text-xl font-bold">

                            Machine History Preview

                        </h3>

                    </div>

                    {{-- Body --}}

                    <div class="p-6 bg-white">

                        <div class="flex items-center justify-between">

                            <div>

                                <h2 class="text-2xl font-bold">

                                    Machine #30001

                                </h2>

                                <p class="text-slate-500">

                                    Wet Wire Drawing Machine

                                </p>

                            </div>

                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">

                                ACTIVE

                            </span>

                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-8">

                            <div class="rounded-xl bg-slate-50 p-4">

                                <p class="text-sm text-slate-500">

                                    Last PM

                                </p>

                                <h4 class="font-bold mt-2">

                                    29 Jul 2026

                                </h4>

                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">

                                <p class="text-sm text-slate-500">

                                    Next PM

                                </p>

                                <h4 class="font-bold mt-2">

                                    29 Aug 2026

                                </h4>

                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">

                                <p class="text-sm text-slate-500">

                                    Problems

                                </p>

                                <h4 class="font-bold mt-2 text-red-600">

                                    2 Findings

                                </h4>

                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">

                                <p class="text-sm text-slate-500">

                                    Spareparts

                                </p>

                                <h4 class="font-bold mt-2 text-blue-600">

                                    3 Items

                                </h4>

                            </div>

                        </div>

                        <div class="mt-8 rounded-xl bg-slate-50 p-5">

                            <h4 class="font-semibold">

                                Recent Maintenance

                            </h4>

                            <ul class="mt-4 space-y-3 text-sm">

                                <li class="flex justify-between">

                                    <span>✔ Bearing Inspection</span>

                                    <span class="text-slate-400">

                                        29 Jul

                                    </span>

                                </li>

                                <li class="flex justify-between">

                                    <span>✔ Greasing</span>

                                    <span class="text-slate-400">

                                        29 Jul

                                    </span>

                                </li>

                                <li class="flex justify-between">

                                    <span>✔ Belt Replacement</span>

                                    <span class="text-slate-400">

                                        29 Jul

                                    </span>

                                </li>

                            </ul>

                        </div>

                        <a href="#"
                            class="mt-8 inline-flex w-full justify-center rounded-xl bg-blue-600 py-4 text-white font-semibold hover:bg-blue-700 transition">

                            View Machine History

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
