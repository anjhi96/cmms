<?php

namespace App\Http\Controllers;
use App\Models\Machine;
use Carbon\Carbon;
use App\Models\MachineMeasurement;
use App\Models\MachineProblem;
use App\Models\MachineProblemFinding;
use App\Models\PMSchedule;
use App\Models\PMMeasurement;
use App\Models\PMProblem;
use App\Models\PMSparepart;
use App\Models\PMChecklist;

class MachineHistoryController extends Controller
{
    public function index()
    {
        $query = PMSchedule::query();

        /*
        nanti role filter kita copy persis dari PMScheduleController
        */

        $machines = $query
            ->select(
                'machine_number',
                'machine_type',
                'area'
            )
            ->selectRaw('MAX(actual_date) as last_pm')
            ->groupBy(
                'machine_number',
                'machine_type',
                'area'
            )
            ->orderBy('machine_number')
            ->paginate(20);

        return view(
            'machine-history.index',
            compact('machines')
        );
    }

    public function show($machineNumber)
    {
        $machine = Machine::where(
            'machine_number',
            $machineNumber
        )->firstOrFail();

        $pmHistories = PMSchedule::where(
                'machine_number',
                $machineNumber
            )
            ->whereNotNull('actual_date')
            ->latest('actual_date')
            ->paginate(10);

        $lastPmRecord = $pmHistories->first();

        $lastPm = $lastPmRecord?->actual_date
            ? Carbon::parse($lastPmRecord->actual_date)
            : null;

        $nextPm = null;

        if ($lastPm) {

            $nextPm = $lastPm->copy();

            switch (strtolower($machine->pm_cycle_unit)) {

                case 'day':
                    $nextPm->addDays($machine->pm_cycle_value);
                    break;

                case 'week':
                    $nextPm->addWeeks($machine->pm_cycle_value);
                    break;

                case 'month':
                    $nextPm->addMonths($machine->pm_cycle_value);
                    break;
            }

        }

        return view(
            'machine-history.show',
            compact(
                'machine',
                'pmHistories',
                'lastPm',
                'nextPm'
            )
        );
    }

    public function detail($machineNumber, PMSchedule $pmSchedule)
    {
        $measurements = MachineMeasurement::where(
            'machine_type',
            $pmSchedule->machine_type
        )
        ->orderBy('measurement_item')
        ->get();

        $pmMeasurements = PMMeasurement::where(
            'pm_schedule_id',
            $pmSchedule->id
        )->get();

        $pmProblems = PMProblem::with([
            'machineProblem',
            'machineProblemFinding'
        ])
        ->where('pm_schedule_id', $pmSchedule->id)
        ->get();

        $pmSpareparts = PMSparepart::with('sparepart')
            ->where('pm_schedule_id', $pmSchedule->id)
            ->get();

        $lastPm = PMSchedule::where('machine_number', $pmSchedule->machine_number)
            ->whereNotNull('actual_date')
            ->where('actual_date', '<', $pmSchedule->actual_date)
            ->latest('actual_date')
            ->value('actual_date');

        $lastPm = $lastPm ? Carbon::parse($lastPm) : null;

        $nextPm = null;

        if ($pmSchedule->actual_date) {

            $nextPm = Carbon::parse($pmSchedule->actual_date);

            $unit = strtolower(trim($pmSchedule->machine->pm_cycle_unit));
            $value = (int) $pmSchedule->machine->pm_cycle_value;

            match ($unit) {
                'day'   => $nextPm->addDays($value),
                'week'  => $nextPm->addWeeks($value),
                'month' => $nextPm->addMonths($value),
                default => null,
            };
        }

        $totalCost = $pmSpareparts->sum(function ($item) {
            return ($item->qty ?? 0) * ($item->sparepart->price ?? 0);
        });

        $checklists = PMChecklist::with('machineChecklist')
            ->where('pm_schedule_id', $pmSchedule->id)
            ->get()
            ->groupBy(function ($item) {
                return $item->machineChecklist->section ?? 'Others';
            });

        return view(
            'machine-history.detail',
            compact(
                'pmSchedule',
                'measurements',
                'pmMeasurements',
                'pmProblems',
                'pmSpareparts',
                'lastPm',
                'nextPm',
                'totalCost',
                'checklists',
            )
        );
    }
}