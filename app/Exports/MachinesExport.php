<?php

namespace App\Exports;

use App\Models\Machine;
use Maatwebsite\Excel\Concerns\FromCollection;

class MachinesExport implements FromCollection
{
    public function collection()
    {
        return Machine::select(
            'machine_number',
            'machine_type',
            'description',
            'status'
        )->get();
    }
}