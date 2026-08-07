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
use App\Models\PMWorkSession;
use App\Models\PMManpower;
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

        $workSessions = PMWorkSession::with('manpowers')
            ->where('pm_schedule_id', $pmSchedule->id)
            ->get();

        if ($workSessions->isEmpty()) {
            $workSessions = collect([
                (object) [
                    'id' => null,
                    'actual_date' => $pmSchedule->actual_date ?? date('Y-m-d'),
                    'start_time' => $pmSchedule->start_time ?? '',
                    'end_time' => $pmSchedule->end_time ?? '',
                    'duration' => $pmSchedule->duration,
                    'manpowers' => collect(),
                ],
            ]);
        }

        $sessions = $workSessions->map(function ($session) {
            return [
                'id' => $session->id,
                'actual_date' => $session->actual_date,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
                'duration' => $session->duration,
                'manpowers' => collect($session->manpowers)->map(function ($manpower) {
                    return [
                        'id' => $manpower->id,
                        'person' => $manpower->person,
                        'start_time' => $manpower->start_time,
                        'end_time' => $manpower->end_time,
                        'duration' => $manpower->duration,
                        'man_hour' => $manpower->man_hour,
                    ];
                })->toArray(),
            ];
        })->toArray();

        $pmSchedule->setRelation('workSessions', $workSessions);

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
            'sessions',
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
        $request->validate([
            'order_number' => 'required',

            'pic' => 'required',

            'actual_date' => 'required_without:sessions|date',

            'start_time' => 'required_without:sessions',

            'end_time' => 'nullable',

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

            'sessions' => 'nullable|array|min:1',
            'sessions.*.actual_date' => 'required|date',
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'nullable|date_format:H:i',
            'sessions.*.id' => 'nullable|integer',
            'sessions.*.manpowers' => 'nullable|array',
            'sessions.*.manpowers.*.id' => 'nullable|integer',
            'sessions.*.manpowers.*.person' => 'required|string|max:255',
            'sessions.*.manpowers.*.start_time' => 'required|date_format:H:i',
            'sessions.*.manpowers.*.end_time' => 'nullable|date_format:H:i',
        ]);

        $sessionData = $request->input('sessions', []);
        $processedSessions = [];
        $totalDuration = null;
        $actualDate = $request->actual_date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        if (is_array($sessionData) && count($sessionData) > 0) {
            $totalDuration = 0;
            $actualDate = null;
            $startTime = null;
            $endTime = null;
            foreach ($sessionData as $session) {
                $sessionDuration = null;

                if (!empty($session['start_time']) && !empty($session['end_time'])) {
                    $start = Carbon::createFromFormat('H:i', $session['start_time']);
                    $end = Carbon::createFromFormat('H:i', $session['end_time']);

                    if ($end->lessThan($start)) {
                        $end->addDay();
                    }

                    $sessionDuration = $start->diffInMinutes($end);
                    $totalDuration += $sessionDuration;
                }

                $processedSessions[] = [
                    'id' => $session['id'] ?? null,
                    'actual_date' => $session['actual_date'],
                    'start_time' => $session['start_time'],
                    'end_time' => $session['end_time'] ?? null,
                    'duration' => $sessionDuration,
                    'manpowers' => $session['manpowers'] ?? [],
                ];

                $actualDate = $actualDate
                    ? max($actualDate, $session['actual_date'])
                    : $session['actual_date'];

                if (!$startTime && $session['start_time']) {
                    $startTime = $session['start_time'];
                }

                if ($session['end_time']) {
                    $endTime = $session['end_time'];
                }
            }
        } else {
            if ($request->start_time && $request->end_time) {
                $start = Carbon::createFromFormat('H:i', $request->start_time);
                $end = Carbon::createFromFormat('H:i', $request->end_time);

                if ($end->lessThan($start)) {
                    $end->addDay();
                }

                $totalDuration = $start->diffInMinutes($end);
            }
        }

        DB::transaction(function () use ($request, $pmSchedule, $processedSessions, $totalDuration, $actualDate, $startTime, $endTime) {
            $pmSchedule->update([
                'order_number' => $request->order_number,
                'actual_date' => $actualDate,
                'pic' => $request->pic,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => $totalDuration,
                'oil_change' => $pmSchedule->requiresOilChange()
                    ? $request->oil_change
                    : null,
                'greasing' => $request->greasing,
                'wo_zsbp' => $request->wo_zsbp,
                'remarks' => $request->remarks,
                'status' => 'IN_PROGRESS',
            ]);

            if (count($processedSessions) > 0) {
                $existingSessions = $pmSchedule->workSessions()
                    ->with('manpowers')
                    ->get()
                    ->keyBy('id');

                $sessionIdsToKeep = [];

                foreach ($processedSessions as $session) {
                    $sessionAttributes = [
                        'actual_date' => $session['actual_date'],
                        'start_time' => $session['start_time'],
                        'end_time' => $session['end_time'],
                        'duration' => $session['duration'],
                    ];

                    if (!empty($session['id']) && $existingSessions->has($session['id'])) {
                        $workSession = $existingSessions->get($session['id']);
                        $workSession->update($sessionAttributes);
                    } else {
                        $workSession = PMWorkSession::create([
                            'pm_schedule_id' => $pmSchedule->id,
                            ...$sessionAttributes,
                        ]);
                    }

                    $sessionIdsToKeep[] = $workSession->id;

                    $existingManpowers = $workSession->manpowers()->get()->keyBy('id');
                    $manpowerIdsToKeep = [];

                    foreach ($session['manpowers'] as $manpower) {
                        $manpowerDuration = null;
                        $manHour = null;

                        if (!empty($manpower['start_time']) && !empty($manpower['end_time'])) {
                            $start = Carbon::createFromFormat('H:i', $manpower['start_time']);
                            $end = Carbon::createFromFormat('H:i', $manpower['end_time']);

                            if ($end->lessThan($start)) {
                                $end->addDay();
                            }

                            $manpowerDuration = $start->diffInMinutes($end);
                            $manHour = $manpowerDuration;
                        }

                        $manpowerAttributes = [
                            'person' => $manpower['person'],
                            'start_time' => $manpower['start_time'],
                            'end_time' => $manpower['end_time'] ?? null,
                            'duration' => $manpowerDuration,
                            'man_hour' => $manHour,
                        ];

                        if (!empty($manpower['id']) && $existingManpowers->has($manpower['id'])) {
                            $pmManpower = $existingManpowers->get($manpower['id']);
                            $pmManpower->update($manpowerAttributes);
                        } else {
                            $pmManpower = PMManpower::create(array_merge([
                                'pm_work_session_id' => $workSession->id,
                            ], $manpowerAttributes));
                        }

                        $manpowerIdsToKeep[] = $pmManpower->id;
                    }

                    PMManpower::where('pm_work_session_id', $workSession->id)
                        ->whereNotIn('id', $manpowerIdsToKeep)
                        ->delete();
                }

                PMWorkSession::where('pm_schedule_id', $pmSchedule->id)
                    ->whereNotIn('id', $sessionIdsToKeep)
                    ->delete();
            }

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