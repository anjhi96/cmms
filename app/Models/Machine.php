<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'machine_number',
        'area',
        'machine_type',
        'description',
        'status',
        'install_date',
        'criticality',
        'remarks',
        'pm_cycle_value',
        'pm_cycle_unit'
    ];
}