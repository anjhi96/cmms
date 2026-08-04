@extends('layouts.app')

@section('title', 'Riwayat Audit Oli')

@section('content')
    @php
        $latestColors = $latestAudit?->conditionColor();
        $barHeights = [
            'OKE' => 'h-7',
            'PANTAU' => 'h-10',
            'HAMPIR_GARIS' => 'h-14',
            'PAS_GARIS' => 'h-16',
            'KRITIS' => 'h-20',
        ];
    @endphp

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('oil-audits.report') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-slate-900"><span aria-hidden="true">←</span> Kembali ke Report Audit Oli</a>
        <a href="{{ route('oil-audits.entry', $machine->machine_number) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 sm:w-fit">Audit mesin ini lagi</a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">{{ session('warning') }}</div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between sm:p-7">
            <div class="min-w-0">
                <h1 class="font-mono text-3xl font-bold tracking-tight text-slate-950">{{ $machine->machine_number }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $machine->machine_type }} · {{ $machine->area }}@if($machine->description) · {{ $machine->description }}@endif</p>
            </div>
            @if ($latestAudit)
                <div class="w-full rounded-2xl px-4 py-3 text-center sm:w-auto {{ $latestColors['badge'] }}">
                    <p class="text-xs font-medium opacity-90">Kondisi terakhir</p>
                    <p class="mt-0.5 text-lg font-bold">{{ $latestAudit->conditionLabel() }}</p>
                </div>
            @else
                <div class="w-full rounded-2xl bg-slate-100 px-4 py-3 text-center text-slate-600 sm:w-auto">
                    <p class="text-xs">Kondisi terakhir</p>
                    <p class="mt-0.5 text-lg font-bold">Belum ada audit</p>
                </div>
            @endif
        </div>

        <div class="p-5 sm:p-7">
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-700">Riwayat kondisi</h2>
                    <p class="mt-1 text-xs text-slate-500">Kiri adalah pengecekan lama, kanan adalah yang terbaru.</p>
                </div>
                <span class="text-xs font-medium text-slate-400">{{ $machine->oilAudits()->count() }} audit</span>
            </div>

            @if ($recentAudits->isNotEmpty())
                <div class="overflow-x-auto pb-2">
                    <div class="flex min-w-[320px] items-end gap-1.5 border-b border-slate-200 pt-4 sm:min-w-[520px]">
                        @foreach ($recentAudits as $audit)
                            @php($colors = $audit->conditionColor())
                            <div class="group flex min-w-0 flex-1 flex-col items-center justify-end">
                                <span class="mb-2 hidden rounded-md bg-slate-900 px-2 py-1 text-[11px] font-semibold text-white shadow-sm group-hover:block">{{ $audit->conditionLabel() }}</span>
                                <div class="w-full min-h-7 rounded-t-xl {{ $barHeights[$audit->condition] ?? 'h-7' }} {{ $colors['bar'] }}" title="{{ $audit->conditionLabel() }}"></div>
                                <p class="mt-2 whitespace-nowrap text-[10px] text-slate-500">{{ $audit->audited_at->format('d M') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-2xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Belum ada riwayat audit oli untuk mesin ini.</div>
            @endif
        </div>
    </section>

    <section class="mt-6">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-900">Detail pengecekan</h2>
            <p class="mt-1 text-sm text-slate-500">Setiap kondisi yang perlu action ditampilkan jelas agar tidak terlewat.</p>
        </div>

        <div class="relative ml-3 border-l border-slate-200 pl-7 sm:ml-4">
            @forelse ($audits as $audit)
                @php($colors = $audit->conditionColor())
                <article class="relative mb-4">
                    <span class="absolute -left-[37px] top-4 h-4 w-4 rounded-full border-2 border-white shadow-sm {{ $colors['dot'] }}"></span>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $colors['badge'] }}">{{ $audit->conditionLabel() }}</span>
                                    <time class="text-sm font-bold text-slate-900">{{ $audit->audited_at->format('d M Y, H:i') }}</time>
                                </div>
                                <p class="mt-2 text-sm text-slate-600">Dicek oleh <span class="font-semibold text-slate-800">{{ $audit->audited_by_name }}</span></p>
                            </div>
                            @if (!$audit->needsFollowUp())
                                <span class="w-fit rounded-lg bg-slate-50 px-3 py-2 text-xs font-medium text-slate-500">Tidak perlu tindak lanjut</span>
                            @elseif ($audit->followUp)
                                <span class="w-fit rounded-lg bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700">✓ Tindak lanjut selesai</span>
                            @else
                                <span class="w-fit rounded-lg bg-orange-50 px-3 py-2 text-xs font-bold text-orange-700">Action PIC diperlukan</span>
                            @endif
                        </div>

                        @if ($audit->needsFollowUp() && $audit->followUp)
                            <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50/70 p-4">
                                <div class="grid gap-3 text-sm sm:grid-cols-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Problem ditemukan</p>
                                        <p class="mt-1 font-medium text-slate-800">{{ $audit->followUp->problem }}</p>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Tindakan dilakukan</p>
                                        <p class="mt-1 whitespace-pre-line text-slate-700">{{ $audit->followUp->action_taken }}</p>
                                    </div>
                                </div>
                                <p class="mt-3 border-t border-emerald-100 pt-3 text-xs text-slate-600">Ditindaklanjuti oleh <span class="font-semibold text-slate-800">{{ $audit->followUp->pic_name }}</span> · {{ $audit->followUp->actioned_at->format('d M Y, H:i') }}</p>
                            </div>
                        @endif

                        @if ($audit->needsFollowUp() && !$audit->followUp)
                            <form method="POST" action="{{ route('oil-audits.follow-up.store', $audit) }}" class="mt-4 rounded-xl border border-orange-200 bg-orange-50/70 p-4">
                                @csrf
                                <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="font-bold text-slate-900">Catat tindak lanjut</h3>
                                        <p class="text-xs text-slate-600">PIC akan tercatat otomatis sebagai {{ auth()->user()->name }}.</p>
                                    </div>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
                                    <div>
                                        <label for="problem-{{ $audit->id }}" class="mb-1.5 block text-xs font-semibold text-slate-700">Problem ditemukan</label>
                                        <select id="problem-{{ $audit->id }}" name="problem" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100">
                                            <option value="">Pilih problem</option>
                                            @foreach ($problemOptions as $problem)
                                                <option value="{{ $problem }}">{{ $problem }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="action-{{ $audit->id }}" class="mb-1.5 block text-xs font-semibold text-slate-700">Tindakan yang dilakukan</label>
                                        <textarea id="action-{{ $audit->id }}" name="action_taken" required rows="3" maxlength="2000" placeholder="Contoh: Tambah oli hingga batas aman dan cek sumber kebocoran." class="w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100"></textarea>
                                    </div>
                                </div>
                                <div class="mt-3 flex justify-end">
                                    <button class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 sm:w-auto">Simpan & tandai selesai</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-10 text-center text-sm text-slate-500">Belum ada audit oli untuk mesin ini.</div>
            @endforelse
        </div>

        <div class="mt-5">{{ $audits->links() }}</div>
    </section>
@endsection
