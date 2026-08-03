<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-4 shadow-sm backdrop-blur sm:px-6 lg:ml-72">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            {{-- Tombol Hamburger: cuma tampil di mobile --}}
            <button @click="$store.sidebar.open = true"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden"
                title="Open Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M4 6h16" />
                    <path d="M4 12h16" />
                    <path d="M4 18h16" />
                </svg>
            </button>

            {{-- Separator: cuma tampil bareng hamburger (mobile) --}}
            <div class="h-6 w-px bg-slate-200 lg:hidden"></div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Maintenance Free</p>
                <h2 class="text-xl font-semibold text-slate-800">{{ $pageTitle ?? 'Dashboard' }}</h2>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <div class="text-right">
                <div class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500">{{ auth()->user()->role }}</div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-red-700">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>