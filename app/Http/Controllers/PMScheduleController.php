<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\PMSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Imports\PMScheduleImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MachineProblem;
use App\Models\MachineMeasurement;
use App\Http\Controllers\PMScheduleController;
use App\Imports\PMSchedulesImport;
use App\Models\MachineProblemFinding;
use App\Models\Sparepart;
use App\Models\PMMeasurement;
use App\Models\PMProblem;
use App\Models\PMSparepart;
use Illuminate\Support\Facades\DB;
use App\Models\MachineChecklist;
use App\Models\PMChecklist;

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
            'file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        Excel::import(new PMScheduleImport(), $request->file('file'));

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
            'plan_date' => 'required|date'
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
            'plan_date'    => $request->plan_date,
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
        $bigProblems = MachineProblem::where(
            'machine_type',
            $pmSchedule->machine_type
        )
        ->orderBy('problem')
        ->get();

        $problemFindings = MachineProblemFinding::all()
        ->groupBy(function ($item) {
            return strtolower(trim($item->category));
        });

        $measurements = MachineMeasurement::where('machine_type', $pmSchedule->machine_type)
            ->orderBy('measurement_item')
            ->get();

        $spareparts = Sparepart::select(
            'id',
            'material_number',
            'description',
            'location',
            'remarks',
            'unit'
        )
        ->orderBy('description')
        ->get();

// ambil data PM yang sudah ada untuk schedule ini
        $pmMeasurements = PMMeasurement::where(
            'pm_schedule_id',
            $pmSchedule->id
        )->get();


        $pmProblems = PMProblem::with([
            'machineProblem',
            'machineProblemFinding'
        ])
        ->where(
            'pm_schedule_id',
            $pmSchedule->id
        )
        ->get();


        $pmSpareparts = PMSparepart::with('sparepart')
        ->where(
            'pm_schedule_id',
            $pmSchedule->id
        )
        ->get();


        return view('pm-schedules.edit', compact(
            'pmSchedule',
            'bigProblems',
            'problemFindings',
            'measurements',
            'spareparts',
// existing PM data
            'pmMeasurements',
            'pmProblems',
            'pmSpareparts'
        ));
    }


    public function update(Request $request, PMSchedule $pmSchedule)
    {
        // VALIDASI INPUT
        $request->validate([
        'order_number' => 'required',

        'actual_date' => 'required|date',

        'pic' => 'required',

        'start_time' => 'required',

        'end_time' => 'required',

        'greasing' => 'required',

        'oil_change' => 'nullable',

        'wo_zsbp' => 'required',

        'remarks' => 'required',

        'problems.*.problem' => 'required',

        'problems.*.finding' => 'required',

        'problems.*.severity' => 'required',

        'measurements.*.measurement_item' => 'required',

        'measurements.*.measurement_value' => 'required',

        'spareparts.*.sparepart_id' => 'required',

        'spareparts.*.qty' => 'required|integer|min:1',

]);
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
        DB::transaction(function () use ($request, $pmSchedule, $duration) {
            // 2. update schedule (header)
            $pmSchedule->update([
                'order_number' => $request->order_number,

                'actual_date' => $request->actual_date,

                'pic' => $request->pic,

                'start_time' => $request->start_time,

                'end_time' => $request->end_time,

                'duration' => $duration,

                'oil_change' => $request->oil_change,

                'greasing' => $request->greasing,

                'wo_zsbp' => $request->wo_zsbp,

                'remarks' => $request->remarks,

                'status' => 'IN_PROGRESS',

            ]);

            // 3. update measurements
            PMMeasurement::where(
                'pm_schedule_id',
                $pmSchedule->id
            )->delete();
            if ($request->measurements) {

                foreach ($request->measurements as $measurement) {

                    PMMeasurement::create([

                    'pm_schedule_id' => $pmSchedule->id,

                    'machine_measurement_id' => $measurement['machine_measurement_id'],

                    'measurement_item' => $measurement['measurement_item'],

                    'standard' => $measurement['standard'],

                    'measurement_value' => $measurement['measurement_value'],

                    'unit' => $measurement['unit'],

                ]);

                }

            }

            PMProblem::where(
                'pm_schedule_id',
                $pmSchedule->id
            )->delete();

            if ($request->problems) {

                foreach ($request->problems as $problem) {

                    if (empty($problem['problem'])) {
                        continue;
                    }

                    PMProblem::create([

                        'pm_schedule_id' => $pmSchedule->id,

                        'machine_problem_id' => $problem['problem'],

                        'machine_problem_finding_id' => $problem['finding'],

                        'severity' => $problem['severity'],

                    ]);

                }

            }

            PMSparepart::where(
                'pm_schedule_id',
                $pmSchedule->id
            )->delete();

            if ($request->spareparts) {

                foreach ($request->spareparts as $item) {

                    if (empty($item['sparepart_id'])) {
                        continue;
                    }

                    PMSparepart::create([

                        'pm_schedule_id' => $pmSchedule->id,

                        'sparepart_id' => $item['sparepart_id'],

                        'qty' => $item['qty'] ?? 1,

                        'unit' => $item['unit'] ?? null,

                    ]);

                }

            }
        });

        return redirect()
    ->route('pm-schedules.checklist', $pmSchedule->id)
    ->with('success', 'PM Progress Saved');

    }

    private function updatePMStatus(PMSchedule $pmSchedule)
    {

        // belum ada PM
        if (!$pmSchedule->actual_date) {

            if (now()->greaterThan($pmSchedule->due_date)) {

                $pmSchedule->update([
                    'status' => 'MISSED'
                ]);

            } else {

                $pmSchedule->update([
                    'status' => 'OPEN'
                ]);

            }

            return;
        }


        // cek apakah checklist sudah disimpan
        $hasChecklist = PMChecklist::where(
            'pm_schedule_id',
            $pmSchedule->id
        )->exists();


        if (!$hasChecklist) {

            $pmSchedule->update([
                'status' => 'IN_PROGRESS'
            ]);

            return;

        }



        // checklist sudah ada
        if (
            Carbon::parse($pmSchedule->actual_date)
            ->greaterThan(
                Carbon::parse($pmSchedule->due_date)
            )
        ) {

            $pmSchedule->update([
                'status' => 'FINISHED'
            ]);

        } else {

            $pmSchedule->update([
                'status' => 'FINISHED_ON_TIME'
            ]);

        }

    }

    public function checklist(PMSchedule $pmSchedule)
    {
        $checklists = MachineChecklist::where(
            'machine_type',
            $pmSchedule->machine_type
        )
        ->orderBy('section_order')
        ->orderBy('item_order')
        ->get();

        return view(
            'pm-schedules.checklist',
            compact(
                'pmSchedule',
                'checklists'
            )
        );
    }

    public function saveChecklist(Request $request, PMSchedule $pmSchedule)
    {
        DB::transaction(function () use ($request, $pmSchedule) {

            // hapus checklist lama jika ada
            PMChecklist::where(
                'pm_schedule_id',
                $pmSchedule->id
            )->delete();


            foreach ($request->checklists as $item) {

                PMChecklist::create([

                    'pm_schedule_id' => $pmSchedule->id,

                    'machine_checklist_id' => $item['machine_checklist_id'],

                    'clean' => $item['clean'] ?? 'NO',

                    'lubrication' => $item['lubrication'] ?? 'NO',

                    'replace' => $item['replace'] ?? 'NO',

                    'check' => $item['check'] ?? 'NO',

                    'remarks' => $item['remarks'] ?? null,

                ]);

            }

        });

        $pmSchedule->refresh();

        $this->updatePMStatus($pmSchedule);


        return redirect()
            ->route('pm-schedules.index')
            ->with('success', 'PM Checklist saved successfully');

    }




    public function destroy(PMSchedule $pmSchedule)
    {
        $pmSchedule->delete();

        return back()->with('success', 'PM Schedule deleted');
    }
}
