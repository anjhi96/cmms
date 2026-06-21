<?php

namespace App\Imports;

use App\Models\Machine;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\PMSchedule;
use App\Models\PMDetail;
use App\Models\PMSparepart;

class MachinesImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {

            $machineNumber = trim($row[0]);

            // skip empty row
            if (!$machineNumber) {
                continue;
            }

            $machineType = trim($row[1] ?? null);
            $area        = trim($row[2] ?? null);
            $status      = strtoupper(trim($row[3] ?? 'ACTIVE'));

            // validasi status
            if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
                $status = 'ACTIVE';
            }

            Machine::updateOrCreate(
                [
                    'machine_number' => $machineNumber
                ],
                [
                    'machine_type' => $machineType,
                    'area'         => $area,
                    'status'       => $status,
                ]
            );
        }
    }
}
