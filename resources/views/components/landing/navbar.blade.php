<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-200">

    <div class="max-w-7xl mx-auto px-6">

        <div class="flex items-center justify-between h-18">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-xl bg-blue-600 flex items-center justify-center shadow">

                    <svg viewBox="0 0 192 192" xmlns="http://www.w3.org/2000/svg" fill="none">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <g style="stroke-width:.903553">
                                <g style="stroke-width:1.22576">
                                    <path d="M6 104V56h34.856M6 80h21.855" class="a"
                                        style="fill:none;stroke:#000000;stroke-width:14.7089;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:none"
                                        transform="matrix(.79792 0 0 .83414 17.08 29.264)"></path>
                                </g>
                                <g style="stroke-width:2.24031;stroke-dasharray:none">
                                    <path
                                        d="M14.665 15.027V7.109h2.574a2.672 2.672 0 1 1 0 5.345h-2.574m5.245 2.573-2.483-2.582"
                                        class="a"
                                        style="fill:none;stroke:#000000;stroke-width:2.24031;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:none"
                                        transform="matrix(5.56244 0 0 5.15795 -16.54 38.912)"></path>
                                </g>
                                <g style="stroke-width:1.03392">
                                    <path d="M6 6h28v0M6 24h28v0M6 42h28v0"
                                        style="fill:none;stroke:#000000;stroke-width:12.4073;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1;stroke-dasharray:none;paint-order:stroke fill markers"
                                        transform="matrix(.86732 0 0 1.07855 141.13 70.11)"></path>
                                </g>
                                <g style="stroke-width:1.03392">
                                    <path d="M6 6h28v0M6 24h28v0M6 42h28v0"
                                        style="fill:none;stroke:#000000;stroke-width:12.4073;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1;stroke-dasharray:none;paint-order:stroke fill markers"
                                        transform="matrix(.86732 0 0 1.07855 103.848 70.12)"></path>
                                </g>
                            </g>
                        </g>
                    </svg>

                </div>

                <div>

                    <h1 class="font-bold text-xl text-slate-800">

                        Maintenance Free

                    </h1>

                    <p class="text-xs text-slate-500">

                        Preventive Maintenance System

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

            </div>

            {{-- Action --}}
            <div class="flex items-center gap-3">

                <a href="{{ route('login') }}"
                    class="hidden md:inline-flex px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-100 transition">

                    Login

                </a>

                <a href="{{ route('qr.scan') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition shadow">

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