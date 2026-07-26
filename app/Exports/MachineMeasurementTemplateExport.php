<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class MachineMeasurementTemplateExport implements FromArray
{
    public function array(): array
    {
        return [

            [
                'machine_type',
                'measurement_item',
                'standard',
                'unit',
            ],

            [
                'NDE',
                'Belt MM',
                '50',
                'Hz',
            ],

            [
                'BFM',
                'Belt LL',
                '60',
                'Hz',
            ],

        ];
    }
}