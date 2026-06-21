<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\PMSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PMDetail;
use App\Models\PMSparepart;
use App\Imports\PMScheduleImport;
use Maatwebsite\Excel\Facades\Excel;

class PMScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = PMSchedule::query();

        // SEARCH
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('machine_number', 'like', '%' . $request->search . '%')
                  ->orWhere('machine_type', 'like', '%' . $request->search . '%');
            });
        }

        // FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // FILTER MONTH
        if ($request->filled('plan_month')) {
            $query->where('plan_month', $request->plan_month);
        }

        // FILTER YEAR
        if ($request->filled('plan_year')) {
            $query->where('plan_year', $request->plan_year);
        }

        $schedules = $query->latest()->paginate(20)->withQueryString();

        // 🔥 SMART MONTH ORDER (FIX URUTAN)
        $months = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        // ambil year unique + sort DESC (smart)
        $years = PMSchedule::select('plan_year')
            ->distinct()
            ->orderBy('plan_year', 'desc')
            ->pluck('plan_year');

        return view('pm-schedules.index', compact(
            'schedules',
            'months',
            'years'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();

        if ($ext !== 'csv') {
            return back()->with('error', 'Only CSV file is allowed!');
        }

        Excel::import(new PMScheduleImport(), $file);

        return back()->with('success', 'PM Schedule imported successfully');
    }

    public function create()
    {
        $machines = Machine::orderBy('machine_number')->get();

        return view('pm-schedules.create', compact('machines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'order_number' => 'required|unique:pm_schedules',
            'plan_month' => 'required',
            'plan_year' => 'required',
            'due_date' => 'required|date',
        ]);

        $machine = Machine::findOrFail($request->machine_id);
        $lastPm = PMSchedule::where('machine_id', $request->machine_id)
            ->where('status', 'DONE')
            ->latest('actual_date')
            ->value('actual_date');

        PMSchedule::create([
            'machine_id'     => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_type'   => $machine->machine_type,
            'area'          => $machine->area,

            'order_number' => $request->order_number,
            'plan_month'   => $request->plan_month,
            'plan_year'    => $request->plan_year,
            'due_date'     => $request->due_date,
            'last_pm'      => $lastPm,
            'pic'          => $request->pic,

            'status' => 'OPEN',
        ]);

        return redirect()
            ->route('pm-schedules.index')
            ->with('success', 'PM Schedule created successfully');
    }

    public function edit(PMSchedule $pmSchedule)
    {
        return view('pm-schedules.edit', compact('pmSchedule'));
    }

    public function update(Request $request, PMSchedule $pmSchedule)
    {
        // 1. hitung duration
        $duration = null;

        if ($request->start_time && $request->end_time) {

            $start = Carbon::createFromFormat('H:i', $request->start_time);
            $end   = Carbon::createFromFormat('H:i', $request->end_time);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $duration = $start->diffInMinutes($end);
        }

        $pmSchedule->update([
            'actual_date' => $request->actual_date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'duration'    => $duration,
            'big_problem' => $request->big_problem,
            'remarks'     => $request->remarks,
            'next_pm'     => $request->next_pm,
            'status'      => $request->status,
        ]);

        // 2. update schedule (header)

        $pmSchedule->update([
            'actual_date' => $request->actual_date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'duration'    => $duration,
            'big_problem' => $request->big_problem,
            'remarks'     => $request->remarks,
            'status'      => $request->status,
        ]);

        // 3. SIMPAN PM DETAIL
        if ($request->details) {
            foreach ($request->details as $detail) {
                PMDetail::create([
                    'pm_schedule_id' => $pmSchedule->id,
                    'measurement_item' => $detail['item'],
                    'measurement_value' => $detail['value'],
                    'unit' => $detail['unit'] ?? null,
                ]);
            }
        }

        // 4. SIMPAN SPAREPART
        if ($request->spareparts) {
            foreach ($request->spareparts as $item) {
                PMSparepart::create([
                    'pm_schedule_id' => $pmSchedule->id,
                    'name' => $item['name'],
                    'qty'  => $item['qty'],
                ]);
            }
        }

        return redirect()->route('pm-schedules.index')
            ->with('success', 'PM updated successfully');
    }

    public function destroy(PMSchedule $pmSchedule)
    {
        $pmSchedule->delete();

        return back()->with('success', 'PM Schedule deleted');
    }
}
