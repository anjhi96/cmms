<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class PMScheduleTemplateExport implements FromArray
{
    public function array(): array
    {
        return [

            [
                'machine_number',
                'machine_type',
                'plan_year',
                'plan_month',
                'plan_date',
                'order_number',
                'status',
            ],

            [
                'MC001',
                'NDE',
                2026,
                'JANUARY',
                '01-01-26',
                '82291121',
                'OPEN',
            ],

        ];
    }
}