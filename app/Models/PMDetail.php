<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PMDetail extends Model
{
    protected $fillable = [
        'pm_schedule_id',
        'measurement_item',
        'measurement_value',
        'unit',
        'remarks',
    ];

    public function pmSchedule()
    {
        return $this->belongsTo(PMSchedule::class);
    }
}
