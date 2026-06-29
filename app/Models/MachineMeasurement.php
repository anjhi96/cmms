<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineMeasurement extends Model
{
    protected $fillable = [
        'machine_type',
        'measurement_item',
        'unit',
    ];
}
