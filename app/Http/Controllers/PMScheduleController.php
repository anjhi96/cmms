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
use App\Imports\PMSchedulesImport;
use App\Models\MachineProblemFinding;
use App\Models\Sparepart;
use App\Models\PMMeasurement;
use App\Models\PMProblem;
use App\Models\PMSparepart;
use Illuminate\Support\Facades\DB;
use App\Models\MachineChecklist;
use App\Models\PMChecklist;
use App\Models\User;

class PMScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = PMSchedule::query();

        $user = auth()->user();

        switch ($user->role) {

            case 'KOORDINATOR WWD':
                $query->where('area', 'WWD');
                break;

            case 'KOORDINATOR BUL':
                $query->where('area', 'BUL');
                break;

            case 'PIC WWD':
            case 'PIC BUL':
                $query->where('pic', $user->name);
                break;

            case 'ADMIN':
            default:
                // lihat semua
                break;
        }

        $picsByArea = [
            'WWD' => User::where('role', 'PIC WWD')
                        ->orderBy('name')
                        ->get(),

            'BUL' => User::where('role', 'PIC BUL')
                        ->orderBy('name')
                        ->get(),
        ];

        // SEARCH
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('machine_number', 'like', '%' . $request->search . '%')
                  ->orWhere('machine_type', 'like', '%' . $request->search . '%');
            });
        }

        // FILTER AREA
        if (
            $user->role === 'ADMIN' &&
            $request->filled('area')
        ) {
            $query->where('area', $request->area);
        }

        // FILTER MACHINE TYPE
        if ($request->filled('machine_type')) {
            $query->where('machine_type', $request->machine_type);
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

        $schedules = $query
            ->orderBy('plan_date', 'asc')
            ->orderBy('machine_number', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Month
        $months = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        // ambil year unique + sort DESC (smart)
        $years = PMSchedule::select('plan_year')
            ->distinct()
            ->orderBy('plan_year', 'desc')
            ->pluck('plan_year');

        // GET UNIQUE AREAS
        $areas = PMSchedule::select('area')
            ->whereNotNull('area')
            ->distinct()
            ->orderBy('area')
            ->pluck('area');

        // GET UNIQUE MACHINE TYPES
        $machineTypes = PMSchedule::select('machine_type')
            ->whereNotNull('machine_type')
            ->distinct()
            ->orderBy('machine_type')
            ->pluck('machine_type');

        return view('pm-schedules.index', compact(
            'schedules',
            'months',
            'years',
            'machineTypes',
            'areas',
            'picsByArea'
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

        $dueDate = Carbon::parse($request->plan_date)->addDays(14);
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'order_number' => 'required|unique:pm_schedules',
            'plan_month' => 'required',
            'plan_year' => 'required',
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
            'due_date'     => $dueDate,
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

        $user = auth()->user();

        if (str_starts_with($user->role, 'PIC')) {

            if ($pmSchedule->pic !== $user->name) {

                abort(403);

            }

        }
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

        $lastPm = PMSchedule::where('machine_number', $pmSchedule->machine_number)
            ->whereNotNull('actual_date')
            ->where('id', '!=', $pmSchedule->id)
            ->latest('actual_date')
            ->value('actual_date');

        $lastPm = $lastPm ? Carbon::parse($lastPm) : null;

        $picRole = match ($pmSchedule->area) {
            'WWD' => 'PIC WWD',
            'BUL' => 'PIC BUL',
            default => null,
        };

        $pics = User::when($picRole, function ($q) use ($picRole) {
                $q->where('role', $picRole);
            })
            ->orderBy('name')
            ->get();

        return view('pm-schedules.edit', compact(
            'pmSchedule',
            'bigProblems',
            'problemFindings',
            'measurements',
            'spareparts',
            'pmMeasurements',
            'pmProblems',
            'pmSpareparts',
            'lastPm',
            'pics'
        ));
    }


    public function update(Request $request, PMSchedule $pmSchedule)
    {
        $user = auth()->user();

        if (str_starts_with($user->role,'PIC')) {

            if ($pmSchedule->pic !== $user->name) {

                abort(403);

            }

        }

        if (!in_array($user->role, ['ADMIN', 'KOORDINATOR WWD', 'KOORDINATOR BUL'])) {
            $request->merge([
                'pic' => $pmSchedule->pic,
            ]);
        }

        // VALIDASI INPUT
        $rules = [
            'order_number' => 'required',
            'pic' => 'required',
            'greasing' => 'nullable',
            'oil_change' => 'nullable',
            'wo_zsbp' => 'nullable',
            'remarks' => 'nullable',
            'problems.*.problem' => 'nullable',
            'problems.*.finding' => 'nullable',
            'problems.*.severity' => 'nullable',
            'measurements.*.measurement_item' => 'nullable',
            'measurements.*.measurement_value' => 'nullable',
            'spareparts.*.sparepart_id' => 'nullable',
            'spareparts.*.qty' => 'nullable|integer|min:1',
        ];

        // If sessions array provided => multi-day flow
        if ($request->has('sessions')) {
            $rules['sessions'] = 'array';
            $rules['sessions.*.actual_date'] = 'required|date';
            $rules['sessions.*.start_time'] = 'required';
            $rules['sessions.*.end_time'] = 'nullable';
        } else {
            // legacy single-day flow
            $rules['actual_date'] = 'required|date';
            $rules['start_time'] = 'required';
            $rules['end_time'] = 'nullable';
        }

        $request->validate($rules);

        // Server-side duration and persistence
        DB::transaction(function () use ($request, $pmSchedule) {

            // Update header (do NOT change actual_date/start_time/end_time/duration for multi-day sessions)
            $updateHeader = [
                'order_number' => $request->order_number,
                'pic' => $request->pic,
                'oil_change' => $pmSchedule->requiresOilChange() ? $request->oil_change : null,
                'greasing' => $request->greasing,
                'wo_zsbp' => $request->wo_zsbp,
                'remarks' => $request->remarks,
                'status' => 'IN_PROGRESS',
            ];

            // Multi-day sessions handling
            if ($request->has('sessions')) {

                $sessionIds = [];

                // existing sessions for selective delete
                $existingIds = $pmSchedule->workSessions()->pluck('id')->toArray();

                foreach ($request->sessions as $s) {

                    $start = Carbon::createFromFormat('H:i', $s['start_time']);
                    $end = $s['end_time'] ? Carbon::createFromFormat('H:i', $s['end_time']) : null;

                    if ($end) {
                        if ($end->lessThan($start)) {
                            $end->addDay();
                        }
                        $duration = $start->diffInMinutes($end);
                    } else {
                        $duration = null;
                    }

                    // If id provided and belongs to this schedule, update; otherwise create
                    if (!empty($s['id'])) {
                        $ws = $pmSchedule->workSessions()->where('id', $s['id'])->first();
                        if ($ws) {
                            $ws->update([
                                'actual_date' => $s['actual_date'],
                                'start_time' => $s['start_time'],
                                'end_time' => $s['end_time'] ?? null,
                                'duration' => $duration,
                            ]);

                            $sessionIds[] = $ws->id;

                            continue;
                        }
                    }

                    $new = $pmSchedule->workSessions()->create([
                        'actual_date' => $s['actual_date'],
                        'start_time' => $s['start_time'],
                        'end_time' => $s['end_time'] ?? null,
                        'duration' => $duration,
                    ]);

                    $sessionIds[] = $new->id;
                }

                // delete removed sessions (selective)
                $toDelete = array_diff($existingIds, $sessionIds);
                if (!empty($toDelete)) {
                    $pmSchedule->workSessions()->whereIn('id', $toDelete)->delete();
                }

                // Update header without touching legacy execution columns
                $pmSchedule->update($updateHeader);

            } else {
                // Legacy single-day behavior (keep existing semantics)
                $duration = null;
                if ($request->start_time && $request->end_time) {
                    $start = Carbon::createFromFormat('H:i', $request->start_time);
                    $end = Carbon::createFromFormat('H:i', $request->end_time);
                    if ($end->lessThan($start)) {
                        $end->addDay();
                    }
                    $duration = $start->diffInMinutes($end);
                }

                $pmSchedule->update(array_merge($updateHeader, [
                    'actual_date' => $request->actual_date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'duration' => $duration,
                ]));
            }

            // 3. update measurements (unchanged)
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



    private function validatePMCompleted(PMSchedule $pmSchedule)
    {
        $errors = [];

        if ($pmSchedule->requiresOilChange() && blank($pmSchedule->oil_change)) {
            $errors[] = 'Oil Change';
        }

        if (blank($pmSchedule->greasing)) {
            $errors[] = 'Greasing';
        }

        if (blank($pmSchedule->wo_zsbp)) {
            $errors[] = 'WO ZSBP';
        }

        if (blank($pmSchedule->remarks)) {
            $errors[] = 'Remarks';
        }

        if (!$pmSchedule->problems()->exists()) {
            $errors[] = 'Problem';
        }

        if (!$pmSchedule->measurements()->exists()) {
            $errors[] = 'Measurement';
        }

        if (!$pmSchedule->spareparts()->exists()) {
            $errors[] = 'Sparepart';
        }

        return $errors;
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

        $pmChecklists = PMChecklist::where(
            'pm_schedule_id',
            $pmSchedule->id
        )->get()
        ->keyBy('machine_checklist_id');

        $nextPm = $pmSchedule->actual_date
            ? Carbon::parse($pmSchedule->actual_date)
            : null;

        if ($nextPm) {

            $unit = strtolower(trim($pmSchedule->machine->pm_cycle_unit));
            $value = (int) $pmSchedule->machine->pm_cycle_value;

            match ($unit) {
                'day'   => $nextPm->addDays($value),
                'week'  => $nextPm->addWeeks($value),
                'month' => $nextPm->addMonths($value),
                'hour'  => $nextPm->addHours($value),
                default => null,
            };
        }

        $spareCost = PMSparepart::with('sparepart')
            ->where('pm_schedule_id', $pmSchedule->id)
            ->get()
            ->sum(function ($item) {
                return ($item->qty ?? 0) * ($item->sparepart->price ?? 0);
            });

        return view(
            'pm-schedules.checklist',
            compact(
                'pmSchedule',
                'checklists',
                'pmChecklists',
                'nextPm',
                'spareCost'
            )
        );
    }

    public function saveChecklist(Request $request, PMSchedule $pmSchedule)
    {

        $errors = $this->validatePMCompleted($pmSchedule);

        if (!empty($errors)) {

            return redirect()
                ->route('pm-schedules.edit', $pmSchedule->id)
                ->with(
                    'warning',
                    'Fill PM belum lengkap : '.implode(', ', $errors)
                );

        }

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

        // If there are work sessions, set completion date to last session's date when checklist is saved
        $lastSessionDate = $pmSchedule->workSessions()->latest('actual_date')->value('actual_date');
        if ($lastSessionDate) {
            $pmSchedule->update([
                'actual_date' => $lastSessionDate,
            ]);
        }

        $pmSchedule->refresh();

        $this->updatePMStatus($pmSchedule);


        return redirect()
            ->route('pm-schedules.index')
            ->with('success', 'PM Checklist saved successfully');

    }

    public function assignPic(Request $request, PMSchedule $pmSchedule)
    {
        $request->validate([
            'pic' => 'nullable|string|max:255',
        ]);

        $pmSchedule->update([
            'pic' => $request->pic,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(PMSchedule $pmSchedule)
    {
        $pmSchedule->delete();

        return back()->with('success', 'PM Schedule deleted');
    }
}