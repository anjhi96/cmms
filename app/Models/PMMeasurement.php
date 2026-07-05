<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PMMeasurement extends Model
{
    protected $fillable = [
        'pm_schedule_id',
        'measurement_id',
        'value',
    ];

    public function pmSchedule()
    {
        return $this->belongsTo(PMSchedule::class);
    }
}
