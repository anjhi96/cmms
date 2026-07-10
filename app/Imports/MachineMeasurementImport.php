<?php

namespace App\Imports;

use App\Models\MachineMeasurement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MachineMeasurementImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            if (
                blank($row['machine_type']) ||
                blank($row['measurement_item'])
            ) {
                continue;
            }

            MachineMeasurement::updateOrCreate(

                [
                    'machine_type'     => trim($row['machine_type']),
                    'measurement_item' => trim($row['measurement_item']),
                ],

                [
                    'standard' => trim($row['standard'] ?? ''),
                    'unit'     => trim($row['unit'] ?? ''),
                ]

            );

        }
    }
}