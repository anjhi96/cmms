<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class MachineChecklistTemplateExport implements FromArray
{
    public function array(): array
    {
        return [

            [
                'machine_type',
                'section',
                'section_order',
                'checklist_item',
                'maintenance_type',
                'item_order',
            ],

            [
                'NDE',
                'Cone 1',
                1,
                'Check cone condition',
                'check',
                1,
            ],

            [
                'BFM',
                'Torsion Shaft',
                1,
                'Greasing bearing',
                'lubrication',
                1,
            ],

        ];
    }
}