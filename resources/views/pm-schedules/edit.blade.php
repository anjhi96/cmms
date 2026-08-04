@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">
        Fill PM Record
    </h1>

    @if (session('warning'))
        <div class="mb-4 rounded border border-red-300 bg-red-100 px-4 py-3 text-red-700">
            <strong>Warning!</strong><br>
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('pm-schedules.update', $pmSchedule) }}"
        class="rounded bg-white p-6 shadow max-sm:p-4">

        <div id="pm-data" data-findings='@json($problemFindings)' data-spareparts='@json($spareparts)'
            data-big-problems='@json($bigProblems)' data-problem-index='@json(isset($pmProblems) ? $pmProblems->count() : 0)'
            data-sparepart-index='@json(isset($pmSpareparts) ? $pmSpareparts->count() : 0)'></div>

        @csrf
        @method('PUT')

        {{-- Machine Information --}}
        <div class="mb-6 rounded-xl bg-linear-to-r from-blue-600 to-blue-700 p-6 text-white shadow max-sm:p-4">
            <h2 class="mb-4 text-xl font-bold">
                Machine Information
            </h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <p class="text-sm opacity-80">Machine Number</p>
                    <p class="text-lg font-semibold">{{ $pmSchedule->machine_number }}</p>
                </div>
                <div>
                    <p class="text-sm opacity-80">Machine Type</p>
                    <p class="text-lg font-semibold">{{ $pmSchedule->machine_type }}</p>
                </div>
                <div>
                    <p class="text-sm opacity-80">Last PM Date</p>
                    <p class="text-lg font-semibold">{{ $lastPm ? $lastPm->format('d-m-Y') : '-' }}</p>
                </div>
            </div>
        </div>

        {{-- PM Information --}}
        <div class="mb-6 rounded-xl bg-white p-6 shadow max-sm:p-4">
            <h3 class="mb-6 text-lg font-semibold">
                PM Information
            </h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium">Order Number</label>
                    <input value="{{ old('order_number', $pmSchedule->order_number) }}" readonly required
                        name="order_number" type="text" class="w-full rounded-lg border bg-gray-100 p-3">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Actual Date</label>
                    <input required name="actual_date" type="date"
                        value="{{ old('actual_date', $pmSchedule->actual_date ?? date('Y-m-d')) }}"
                        class="w-full rounded-lg border p-3">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">PIC PM</label>

                    @php
                        $canEditPic = in_array(auth()->user()->role, ['ADMIN', 'KOORDINATOR WWD', 'KOORDINATOR BUL']);
                    @endphp

                    <select name="pic"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 {{ !$canEditPic ? 'cursor-not-allowed bg-gray-100' : '' }}"
                        {{ !$canEditPic ? 'disabled' : '' }}>
                        <option value="">-- Select PIC --</option>
                        @foreach ($pics as $pic)
                            <option value="{{ $pic->name }}"
                                {{ old('pic', $pmSchedule->pic) == $pic->name ? 'selected' : '' }}>
                                {{ $pic->name }}
                            </option>
                        @endforeach
                    </select>

                    @if (!$canEditPic)
                        <input type="hidden" name="pic" value="{{ $pmSchedule->pic }}">
                    @endif
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Start PM</label>
                    <input required name="start_time" type="time" id="start_time"
                        value="{{ $pmSchedule->start_time ? \Carbon\Carbon::parse($pmSchedule->start_time)->format('H:i') : '' }}"
                        class="w-full rounded-lg border p-3">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">End PM</label>
                    <input name="end_time" type="time" id="end_time"
                        value="{{ $pmSchedule->end_time ? \Carbon\Carbon::parse($pmSchedule->end_time)->format('H:i') : '' }}"
                        class="w-full rounded-lg border p-3">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Duration (Hours)</label>
                    <input name="duration" type="text" id="duration" readonly
                        value="{{ $pmSchedule->duration_formatted }}" class="w-full rounded-lg border bg-gray-100 p-3">
                    <p id="duration_error" class="mt-1 hidden text-sm text-red-500">
                        End time cannot be earlier than start time
                    </p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                @if ($pmSchedule->requiresOilChange())
                    <div>
                        <label class="mb-2 block text-sm font-medium">Oil Change</label>
                        <select name="oil_change" class="w-full rounded-lg border p-3">
                            <option value="">-- Select --</option>
                            <option value="YES" @if ($pmSchedule->oil_change == 'YES') selected @endif>YES</option>
                            <option value="NO" @if ($pmSchedule->oil_change == 'NO') selected @endif>NO</option>
                        </select>
                    </div>
                @endif

                <div>
                    <label class="mb-2 block text-sm font-medium">Greasing</label>
                    <select name="greasing" class="w-full rounded-lg border p-3">
                        <option value="">-- Select --</option>
                        <option value="YES" @if ($pmSchedule->greasing == 'YES') selected @endif>YES</option>
                        <option value="NO" @if ($pmSchedule->greasing == 'NO') selected @endif>NO</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">WO ZSBP</label>
                    <select name="wo_zsbp" class="w-full rounded-lg border p-3">
                        <option value="">-- Select --</option>
                        <option value="YES" @if ($pmSchedule->wo_zsbp == 'YES') selected @endif>YES</option>
                        <option value="NO" @if ($pmSchedule->wo_zsbp == 'NO') selected @endif>NO</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 gap-3">
                <label class="mb-2 block text-sm font-medium">Remarks</label>
                <textarea name="remarks" rows="3" class="w-full rounded-lg border p-3"
                    placeholder="Contoh: Kapstan 1,2 oblak, kapstan 3 bocor, ganti ring kapstan 1 dan 4, ganti nylon, ganti spindel, ganti countersheave, gant belt 1200, 1800, 1500">{{ old('remarks', $pmSchedule->remarks) }}</textarea>
            </div>
        </div>

        {{-- PM Result (Problems) --}}
        <div class="mb-6 rounded-xl bg-white p-6 shadow max-sm:p-4">
            <h3 class="mb-6 text-lg font-semibold">
                PM Result
            </h3>

            <div id="problem-wrapper">
                @forelse($pmProblems as $i => $oldProblem)
                    <div class="problem-row mb-2 flex gap-2 max-sm:mb-3 max-sm:flex-col max-sm:rounded-lg max-sm:border max-sm:border-gray-200 max-sm:bg-gray-50 max-sm:p-3">
                        <select name="problems[{{ $i }}][problem]" class="problem-select w-1/2 rounded border p-2 max-sm:w-full">
                            <option value="">-- Select Problem --</option>
                            @foreach ($bigProblems as $problem)
                                <option value="{{ $problem->id }}" data-category="{{ strtolower(trim($problem->category)) }}"
                                    {{ $oldProblem->machine_problem_id == $problem->id ? 'selected' : '' }}>
                                    {{ $problem->problem }}
                                </option>
                            @endforeach
                        </select>

                        <select name="problems[{{ $i }}][finding]" class="finding-select flex-1 rounded border p-2 max-sm:w-full"
                            data-old-finding="{{ $oldProblem->machine_problem_finding_id }}">
                            <option value="">-- Finding --</option>
                        </select>

                        <select name="problems[{{ $i }}][severity]" class="w-1/5 rounded border p-2 max-sm:w-full">
                            <option value="">-- Severity --</option>
                            <option value="Low" {{ $oldProblem->severity == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ $oldProblem->severity == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ $oldProblem->severity == 'High' ? 'selected' : '' }}>High</option>
                        </select>

                        <button type="button" onclick="removeProblem(this)"
                            class="rounded bg-red-500 px-3 text-white max-sm:w-full max-sm:py-2">
                            X
                        </button>
                    </div>
                @empty
                    <div class="problem-row mb-2 flex gap-2 max-sm:mb-3 max-sm:flex-col max-sm:rounded-lg max-sm:border max-sm:border-gray-200 max-sm:bg-gray-50 max-sm:p-3">
                        <select name="problems[${problemIndex}][problem]" class="problem-select w-1/2 rounded border p-2 max-sm:w-full">
                            <option value="">-- Select Problem --</option>
                            @foreach ($bigProblems as $problem)
                                <option value="{{ $problem->id }}" data-category="{{ strtolower(trim($problem->category)) }}">
                                    {{ $problem->problem }}
                                </option>
                            @endforeach
                        </select>

                        <select name="problems[${problemIndex}][finding]" class="finding-select flex-1 rounded border p-2 max-sm:w-full">
                            <option value="">-- Finding --</option>
                        </select>

                        <select name="problems[${problemIndex}][severity]" class="w-1/5 rounded border p-2 max-sm:w-full">
                            <option value="">-- Severity --</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>

                        <button type="button" onclick="removeProblem(this)"
                            class="rounded bg-red-500 px-3 text-white max-sm:w-full max-sm:py-2">
                            X
                        </button>
                    </div>
                @endforelse
            </div>

            <button type="button" onclick="addProblem()"
                class="mt-2 rounded bg-green-600 px-3 py-1 text-white max-sm:w-full max-sm:py-2">
                + Add Problem
            </button>
        </div>

        {{-- PM Measurement --}}
        <div class="mb-6 rounded-lg bg-white p-6 shadow max-sm:p-4">
            <h2 class="mb-4 text-lg font-semibold">
                Measurement
            </h2>

            {{-- Desktop: tabel --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left">Measurement Item</th>
                            <th class="border px-3 py-2 text-center">Standard</th>
                            <th class="border px-3 py-2 text-center">Actual</th>
                            <th class="border px-3 py-2 text-center">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($measurements as $i => $item)
                            @php
                                $oldMeasurement = $pmMeasurements->where('machine_measurement_id', $item->id)->first();
                            @endphp
                            <tr>
                                <td class="border px-3 py-2">
                                    {{ $item->measurement_item }}
                                    <input type="hidden" name="measurements[{{ $i }}][machine_measurement_id]" value="{{ $item->id }}">
                                    <input type="hidden" name="measurements[{{ $i }}][measurement_item]" value="{{ $item->measurement_item }}">
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    {{ $item->standard ?? '-' }}
                                    <input type="hidden" name="measurements[{{ $i }}][standard]" value="{{ $item->standard }}">
                                </td>
                                <td class="border px-3 py-2">
                                    <input type="text" name="measurements[{{ $i }}][measurement_value]"
                                        value="{{ $oldMeasurement->measurement_value ?? '' }}"
                                        class="w-full rounded border px-2 py-1 text-center">
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    {{ $item->unit }}
                                    <input type="hidden" name="measurements[{{ $i }}][unit]" value="{{ $item->unit }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border py-5 text-center text-gray-500">No Measurement Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: card per item --}}
            <div class="space-y-3 md:hidden">
                @forelse ($measurements as $i => $item)
                    @php
                        $oldMeasurement = $pmMeasurements->where('machine_measurement_id', $item->id)->first();
                    @endphp
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <div class="mb-2 flex items-center justify-between">
                            <div class="text-sm font-medium text-gray-800">{{ $item->measurement_item }}</div>
                            <div class="text-xs text-gray-500">Std: {{ $item->standard ?? '-' }} {{ $item->unit }}</div>
                        </div>

                        <input type="hidden" name="measurements[{{ $i }}][machine_measurement_id]" value="{{ $item->id }}">
                        <input type="hidden" name="measurements[{{ $i }}][measurement_item]" value="{{ $item->measurement_item }}">
                        <input type="hidden" name="measurements[{{ $i }}][standard]" value="{{ $item->standard }}">
                        <input type="hidden" name="measurements[{{ $i }}][unit]" value="{{ $item->unit }}">

                        <div class="flex items-center gap-2">
                            <input type="text" name="measurements[{{ $i }}][measurement_value]"
                                value="{{ $oldMeasurement->measurement_value ?? '' }}"
                                placeholder="Masukkan nilai actual"
                                class="w-full rounded border bg-white px-3 py-2 text-center">
                            <span class="shrink-0 text-sm text-gray-500">{{ $item->unit }}</span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border py-5 text-center text-gray-500">
                        No Measurement Found
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sparepart Usage --}}
        <div class="mt-6 rounded bg-white p-4 shadow">
            <h2 class="mb-4 text-lg font-bold">
                Sparepart Usage
            </h2>

            <div id="sparepart-wrapper">
                @forelse($pmSpareparts as $i => $oldSpare)
                    <div class="sparepart-row mb-2 flex gap-2 max-sm:mb-3 max-sm:flex-col max-sm:rounded-lg max-sm:border max-sm:border-gray-200 max-sm:bg-gray-50 max-sm:p-3">
                        <select name="spareparts[{{ $i }}][sparepart_id]" class="sparepart-select w-2/3 rounded border p-2 max-sm:w-full"
                            placeholder="Select Sparepart" data-selected="{{ $oldSpare->sparepart_id }}">
                            <option value="{{ $oldSpare->sparepart_id }}">
                                {{ $oldSpare->sparepart->material_number }} - {{ $oldSpare->sparepart->description }}
                            </option>
                        </select>

                        <input type="number" name="spareparts[{{ $i }}][qty]" value="{{ $oldSpare->qty }}"
                            class="w-1/3 rounded border p-2 max-sm:w-full">

                        <button type="button" onclick="removeSparepart(this)"
                            class="rounded bg-red-500 px-3 text-white max-sm:w-full max-sm:py-2">
                            X
                        </button>
                    </div>
                @empty
                    <div class="sparepart-row mb-2 flex gap-2 max-sm:mb-3 max-sm:flex-col max-sm:rounded-lg max-sm:border max-sm:border-gray-200 max-sm:bg-gray-50 max-sm:p-3">
                        <select name="spareparts[${sparepartIndex}][sparepart_id]" placeholder="Select Sparepart"
                            class="sparepart-select w-2/3 rounded border p-2 max-sm:w-full">
                        </select>

                        <input type="number" name="spareparts[${sparepartIndex}][qty]" placeholder="Qty"
                            class="w-1/3 rounded border p-3 max-sm:w-full">

                        <button type="button" onclick="removeSparepart(this)"
                            class="rounded bg-red-500 px-3 text-white max-sm:w-full max-sm:py-2">
                            X
                        </button>
                    </div>
                @endforelse
            </div>

            <button type="button" onclick="addSparepart()"
                class="mt-2 rounded bg-green-600 px-3 py-1 text-white max-sm:w-full max-sm:py-2">
                + Add Sparepart
            </button>
        </div>

        <div class="mt-8 flex justify-end gap-3 max-sm:flex-col">
            <a href="{{ route('pm-schedules.checklist', $pmSchedule->id) }}"
                class="rounded bg-blue-600 px-6 py-3 text-center text-white hover:bg-blue-700 max-sm:w-full">
                Go To Checklist (Dev)
            </a>

            <button type="submit" class="rounded bg-gray-500 px-6 py-3 text-white hover:bg-green-700 max-sm:w-full">
                Lihat Checklist
            </button>
        </div>

    </form>
@endsection