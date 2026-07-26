<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class MachineProblemTemplateExport implements FromArray
{
    public function array(): array
    {
        return [

            [
                'machine_type',
                'problem',
                'category',
            ],

            [
                'NDE',
                'Capstan 1',
                'Capstan',
            ],

            [
                'BFM',
                'Torsion Shaft PO',
                'Torsion Shaft',
            ],

        ];
    }
}