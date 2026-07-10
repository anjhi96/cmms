<?php

namespace App\Imports;

use App\Models\MachineProblem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MachineProblemImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            if (
                blank($row['machine_type']) ||
                blank($row['category']) ||
                blank($row['problem'])
            ) {
                continue;
            }

            MachineProblem::firstOrCreate(

                [
                    'machine_type' => trim($row['machine_type']),
                    'problem'      => trim($row['problem']),
                    'category'     => trim($row['category']),
                ],

            );

        }
    }
}