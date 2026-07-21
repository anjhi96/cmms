<?php

namespace App\Imports;

use App\Models\Machine;
use App\Models\PMSchedule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;

class PMScheduleImport implements ToCollection
{
    public function collection(Collection $rows)
    {

        // dd('PM IMPORT KEPAKE');
        foreach ($rows->skip(1) as $row) {

            $machineNumber = trim($row[0]);

            // skip kalau machine number kosong
            if (!$machineNumber) {
                continue;
            }

            $machine = Machine::where(
                'machine_number',
                $machineNumber
            )->first();

            // skip kalau machine tidak ada
            if (!$machine) {
                continue;
            }

            // normalize date
            try {
                $planDate = Carbon::parse($row[4])->format('Y-m-d');
            } catch (\Exception $e) {
                $planDate = null;
            }

            // 🚨 CHECK DUPLICATE (INI INTI FIX)
            $exists = PMSchedule::where('machine_number', $machineNumber)
                ->where('plan_date', $planDate)
                ->exists();

            if ($exists) {
                continue; // skip kalau sudah ada
            }

            PMSchedule::updateOrCreate([
                'machine_number' => $machineNumber,
                'plan_date'      => $planDate
            ], [
                'machine_id'     => $machine->id,
                'machine_number' => $row[0],
                'machine_type'   => $row[1],
                'area'           => $machine->area,
                'plan_year'      => $row[2],
                'plan_month'     => $row[3],
                'plan_date'      => $row[4], // 🔥 NEW
                'due_date'       => $row[4] ? \Carbon\Carbon::parse($row[4])->addDays(14) : null,
                'status'         => $row[5] ?? 'OPEN',
            ]);
        }
    }
}
