<footer class="bg-slate-900 text-slate-300">

    <div class="max-w-7xl mx-auto px-6 py-16">

        <div class="grid lg:grid-cols-3 gap-10">

            <div>

                <h2 class="text-2xl font-bold text-white">

                    CMMS

                </h2>

                <p class="mt-5 leading-8">

                    Computerized Maintenance Management System
                    untuk membantu tim maintenance melakukan
                    Preventive Maintenance secara digital,
                    cepat, dan terdokumentasi.

                </p>

            </div>

            <div>

                <h3 class="font-semibold text-white">

                    Features

                </h3>

                <ul class="space-y-3 mt-5">

                    <li><a href="#features" class="hover:text-white">PM Schedule</a></li>

                    <li><a href="#features" class="hover:text-white">Digital Checklist</a></li>

                    <li><a href="#features" class="hover:text-white">Machine History</a></li>

                    <li><a href="#features" class="hover:text-white">QR Scanner</a></li>

                </ul>

            </div>

            <div>

                <h3 class="font-semibold text-white">

                    Quick Access

                </h3>

                <ul class="space-y-3 mt-5">

                    <li><a href="{{ route('login') }}" class="hover:text-white">Login</a></li>

                    <li><a href="{{ route('qr.scan') }}" class="hover:text-white">Scan Mesin</a></li>

                </ul>

            </div>

        </div>

        <div class="border-t border-slate-700 mt-12 pt-8 flex flex-col md:flex-row justify-between">

            <p>

                © {{ date('Y') }} CMMS. All Rights Reserved.

            </p>

            <p class="mt-3 md:mt-0">

                Built with ❤️ using Laravel

            </p>

        </div>

    </div>

</footer>