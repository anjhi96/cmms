<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-100 antialiased dark:bg-slate-950">
        <div class="relative grid min-h-screen lg:grid-cols-2">

            {{-- Left panel: branded, dark slate like the app sidebar --}}
            <div class="relative hidden flex-col justify-between overflow-hidden bg-slate-900 p-10 text-white lg:flex">
                {{-- Decorative gradient blobs --}}
                <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-gradient-to-br from-emerald-400/30 to-cyan-500/30 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 -right-10 h-72 w-72 rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 blur-3xl"></div>
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(16,185,129,0.08),transparent_60%)]"></div>

                <a href="{{ route('home') }}" class="relative z-20 flex items-center gap-3" wire:navigate>
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-cyan-500 text-slate-950 shadow-lg shadow-emerald-500/20">
                        <x-app-logo-icon class="h-7 w-7" />
                    </span>
                    <div>
                        <div class="text-base font-semibold tracking-wide">FreeDOMS</div>
                        <div class="text-xs text-slate-400">Preventive Maintenance System</div>
                    </div>
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-3">
                        <flux:heading size="lg" class="text-slate-100">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                        <footer class="text-sm text-slate-400">— {{ trim($author) }}</footer>
                    </blockquote>

                    <div class="mt-8 flex items-center gap-6 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Reliable
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span> Efficient
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span> Modern
                        </span>
                    </div>
                </div>
            </div>

            {{-- Right panel: form --}}
            <div class="flex flex-col items-center justify-center px-6 py-10 sm:px-10">
                <div class="w-full max-w-sm">

                    {{-- Mobile logo --}}
                    <a href="{{ route('home') }}" class="z-20 mb-8 flex flex-col items-center gap-2 lg:hidden" wire:navigate>
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-cyan-500 text-slate-950 shadow-lg shadow-emerald-500/20">
                            <x-app-logo-icon class="h-6 w-6" />
                        </span>
                        <span class="text-base font-semibold tracking-wide text-slate-800 dark:text-white">FreeDOMS</span>
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>