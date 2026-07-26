<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class SparepartTemplateExport implements FromArray
{
    public function array(): array
    {
        return [

            [
                'material_number',
                'location',
                'description',
                'remarks',
                'stock',
                'unit',
                'rop',
                'mrp_type',
                'price',
                'status',
                'machine_type',
                'segment',
                'pdt',
            ],

            [
                'MAT-0001',
                'WH01',
                'Bearing 6205',
                'Motor drive bearing',
                10,
                'PCS',
                2,
                'PD',
                50000,
                'ACTIVE',
                'NDE',
                'KA-WWD',
                'MECHANICAL',
            ],

        ];
    }
}