<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class MachineProblemFindingTemplateExport implements FromArray
{
    public function array(): array
    {
        return [

            [
                'category',
                'finding',
            ],

            [
                'Capstan',
                'Oblak',
            ],

            [
                'Torsion Shaft',
                'Kasar',
            ],

        ];
    }
}