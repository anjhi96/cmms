<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-4 shadow-sm backdrop-blur sm:px-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Maintenance Free</p>
            <h2 class="text-xl font-semibold text-slate-800">{{ $pageTitle ?? 'Dashboard' }}</h2>
        </div>

        <div class="flex items-center gap-3">
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