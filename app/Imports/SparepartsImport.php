<?php

namespace App\Imports;

use App\Models\Sparepart;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;

class SparepartsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {

            $materialNumber = trim($row[0]);

            // skip empty row
            if (!$materialNumber) {
                continue;
            }

            Sparepart::updateOrCreate(

                [
                    'material_number' => $materialNumber
                ],

                [
                    'location'     => trim($row[1] ?? null),
                    'description'  => trim($row[2] ?? null),
                    'remarks'      => trim($row[3] ?? null),

                    'stock'        => $row[4] ?? 0,
                    'unit'         => trim($row[5] ?? null),
                    'rop'          => $row[6] ?? 0,

                    'mrp_type'     => trim($row[7] ?? null),
                    'price'        => $row[8] ?? 0,

                    'status'       => strtoupper(trim($row[9] ?? 'ACTIVE')),

                    'machine_type' => trim($row[10] ?? null),

                    'segment'      => trim($row[11] ?? null),

                    'pdt'          => trim($row[12] ?? null),
                ]
            );
        }
    }
}