@extends('layouts.app')

@section('title', 'Report Audit Oli')

@section('content')
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">Audit Oli</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Report Audit Oli</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau kondisi terakhir dan riwayat pengecekan mesin area WWD.</p>
        </div>
        <a href="{{ route('oil-audits.scan') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 lg:w-fit">
            <span class="text-lg leading-none">⌁</span> Mulai Audit dengan QR
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-sky-100 bg-sky-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Audit hari ini</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['today'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Pengecekan telah direkam</p>
        </div>
        <a href="{{ route('oil-audits.report', ['follow_up' => 1]) }}" class="rounded-2xl border border-orange-100 bg-orange-50 p-4 transition hover:border-orange-300">
            <p class="text-xs font-semibold uppercase tracking-wide text-orange-700">Butuh tindak lanjut</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['pending'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Hampir garis, pas garis, atau kritis</p>
        </a>
        <a href="{{ route('oil-audits.report', ['condition' => 'KRITIS']) }}" class="rounded-2xl border border-red-100 bg-red-50 p-4 transition hover:border-red-300">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Kritis belum ditangani</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['critical'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Perlu prioritas perbaikan</p>
        </a>
    </div>

    @if ($pendingAudits->isNotEmpty())
        <section class="mb-6 rounded-2xl border border-orange-200 bg-orange-50/60 p-4 sm:p-5">
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-slate-900">Follow up belum selesai <span class="text-slate-500">({{ $summary['pending'] }})</span></h2>
                    <p class="mt-0.5 text-xs text-slate-600">Prioritaskan kondisi oli yang membutuhkan action.</p>
                </div>
                <a href="{{ route('oil-audits.report', ['follow_up' => 1]) }}" class="text-sm font-semibold text-orange-700 hover:text-orange-900">Lihat semua</a>
            </div>
            <div class="grid gap-2 md:grid-cols-2">
                @foreach ($pendingAudits as $audit)
                    @php($colors = $audit->conditionColor())
                    <a href="{{ route('oil-audits.history', $audit->machine_number) }}" class="flex items-center justify-between gap-3 rounded-xl border border-white bg-white px-3 py-3 transition hover:border-orange-300 hover:shadow-sm">
                        <div class="min-w-0">
                            <p class="font-mono text-sm font-bold text-slate-900">{{ $audit->machine_number }}</p>
                            <p class="truncate text-xs text-slate-500">Dicek {{ $audit->audited_at->format('d M Y, H:i') }} · {{ $audit->audited_by_name }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold {{ $colors['badge'] }}">{{ $audit->conditionLabel() }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <form method="GET" class="mb-5 flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-3">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nomor, tipe, atau deskripsi mesin" class="w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100 sm:min-w-[220px] sm:flex-1">
        <select name="machine_type" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 sm:w-auto">
            <option value="">Semua tipe mesin</option>
            @foreach ($machineTypes as $type)
                <option value="{{ $type }}" @selected(request('machine_type') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        <select name="condition" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 sm:w-auto">
            <option value="">Semua kondisi terakhir</option>
            @foreach (\App\Models\OilAudit::CONDITION_LABELS as $value => $label)
                <option value="{{ $value }}" @selected(request('condition') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <label class="flex w-full items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 sm:w-auto">
            <input type="checkbox" name="follow_up" value="1" @checked(request('follow_up')) class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
            Perlu follow up
        </label>
        <button class="flex-1 rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 sm:flex-none">Filter</button>
        <a href="{{ route('oil-audits.report') }}" class="flex-1 rounded-xl px-3 py-2.5 text-center text-sm font-semibold text-slate-500 hover:text-slate-900 sm:flex-none">Reset</a>
    </form>

    {{-- Desktop: tabel ringkas --}}
    <div class="hidden overflow-hidden rounded-2xl border border-slate-200 md:block">
        <div class="grid grid-cols-[minmax(130px,1fr)_110px_100px] gap-4 border-b border-slate-200 bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 sm:grid-cols-[minmax(170px,1.5fr)_minmax(150px,1fr)_150px_120px]">
            <span>Mesin</span>
            <span class="hidden sm:block">Audit terakhir</span>
            <span>Kondisi</span>
            <span class="text-right">Riwayat</span>
        </div>
        @forelse ($machines as $machine)
            @php($audit = $machine->latestOilAudit)
            @php($colors = $audit?->conditionColor())
            <div class="grid grid-cols-[minmax(130px,1fr)_110px_100px] items-center gap-4 border-b border-slate-100 px-4 py-4 last:border-b-0 sm:grid-cols-[minmax(170px,1.5fr)_minmax(150px,1fr)_150px_120px]">
                <div class="min-w-0">
                    <p class="font-mono text-sm font-bold text-slate-900">{{ $machine->machine_number }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $machine->machine_type }} · {{ $machine->area }}@if($machine->description) · {{ $machine->description }}@endif</p>
                </div>
                <div class="hidden text-sm text-slate-600 sm:block">
                    @if ($audit)
                        <p>{{ $audit->audited_at->format('d M Y, H:i') }}</p>
                        <p class="text-xs text-slate-400">{{ $audit->audited_by_name }}</p>
                    @else
                        <span class="text-slate-400">Belum diaudit</span>
                    @endif
                </div>
                <div>
                    @if ($audit)
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $colors['badge'] }}">{{ $audit->conditionLabel() }}</span>
                        @if ($audit->needsFollowUp() && !$audit->followUp)
                            <span class="mt-1 block text-[11px] font-medium text-orange-700">Follow up diperlukan</span>
                        @endif
                    @else
                        <span class="text-sm text-slate-400">—</span>
                    @endif
                </div>
                <div class="text-right">
                    <a href="{{ route('oil-audits.history', $machine->machine_number) }}" class="inline-flex rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">Buka</a>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-sm text-slate-500">Tidak ada mesin yang sesuai dengan filter.</div>
        @endforelse
    </div>

    {{-- Mobile: kartu per mesin seperti PM Schedule --}}
    <div class="space-y-3 md:hidden">
        @forelse ($machines as $machine)
            @php($audit = $machine->latestOilAudit)
            @php($colors = $audit?->conditionColor())
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-mono text-base font-bold text-slate-900">{{ $machine->machine_number }}</p>
                        <p class="mt-0.5 truncate text-xs text-slate-500">{{ $machine->machine_type }} · {{ $machine->area }}@if($machine->description) · {{ $machine->description }}@endif</p>
                    </div>
                    @if ($audit)
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold {{ $colors['badge'] }}">{{ $audit->conditionLabel() }}</span>
                    @else
                        <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">Belum audit</span>
                    @endif
                </div>

                <div class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 border-t border-slate-100 pt-3 text-xs">
                    <div>
                        <p class="text-slate-400">Audit terakhir</p>
                        <p class="mt-0.5 font-medium text-slate-700">{{ $audit ? $audit->audited_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Pemeriksa</p>
                        <p class="mt-0.5 truncate font-medium text-slate-700">{{ $audit?->audited_by_name ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                    @if ($audit && $audit->needsFollowUp() && !$audit->followUp)
                        <span class="text-xs font-semibold text-orange-700">Follow up diperlukan</span>
                    @elseif ($audit && $audit->followUp)
                        <span class="text-xs font-semibold text-emerald-700">✓ Action selesai</span>
                    @else
                        <span class="text-xs text-slate-400">&nbsp;</span>
                    @endif
                    <a href="{{ route('oil-audits.history', $machine->machine_number) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">Buka riwayat</a>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-10 text-center text-sm text-slate-500">Tidak ada mesin yang sesuai dengan filter.</div>
        @endforelse
    </div>

    <div class="mt-5">{{ $machines->links() }}</div>
@endsection
