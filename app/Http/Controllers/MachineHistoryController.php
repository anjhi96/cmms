<?php

namespace App\Http\Controllers;

use App\Models\PMSchedule;

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
}