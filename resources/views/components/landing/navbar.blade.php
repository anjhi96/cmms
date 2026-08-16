<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-200">

    <div class="max-w-7xl mx-auto px-6">

        <div class="flex items-center justify-between h-18">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center shadow-md shadow-blue-500/20 text-white">
                    <x-app-logo-icon class="h-6 w-6 text-white" />
                </div>

                <div>

                    <h1 class="font-bold text-xl text-slate-800">

                        FreeDOMS

                    </h1>

                    <p class="text-xs text-slate-500">

                        Preventive Maintenance

                    </p>

                </div>

            </a>

            {{-- Menu Desktop --}}
            <div class="hidden lg:flex items-center gap-8">

                <a href="#features" class="text-slate-600 hover:text-blue-600 transition">

                    Features

                </a>

                <a href="#how-it-works" class="text-slate-600 hover:text-blue-600 transition">

                    How It Works

                </a>

                <a href="#testimonial" class="text-slate-600 hover:text-blue-600 transition">

                    Testimonial

                </a>

                <a href="{{ route('dashboard-guest') }}" class="text-slate-600 hover:text-blue-600 transition">

                    Dashboard

                </a>

            </div>

            {{-- Action --}}
            <div class="flex items-center gap-3">

                <a href="{{ route('login') }}"
                    class="hidden md:inline-flex px-3 py-2 rounded-xl border border-slate-300 hover:bg-slate-100 transition">

                    Login

                </a>

                <a href="{{ route('qr.scan') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition shadow">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7V4h3M20 7V4h-3M4 17v3h3M20 17v3h-3M8 8h2v2H8zM14 8h2v2h-2zM8 14h2v2H8zM14 14h2v2h-2z" />

                    </svg>

                    Scan Mesin

                </a>

            </div>

        </div>

    </div>

</nav>