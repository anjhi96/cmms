<?php

namespace App\Imports;

use App\Models\PMSchedule;
use App\Models\Machine;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PMScheduleImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {

            $machine = Machine::where('machine_number', $row[0])->first();

            if (!$machine) {
                continue;
            }
            // if (!$machine) {
            //     dd('Machine not found: ' . $row[0]);
            // }

            PMSchedule::create([
                'machine_id'     => $machine->id,
                'machine_number' => $row[0],
                'machine_type'   => $row[1],
                'area'          => $machine->area,

                'plan_year'  => $row[2],
                'plan_month' => $row[3],

                'status'     => $row[4] ?? 'OPEN',
            ]);
        }
    }
}
