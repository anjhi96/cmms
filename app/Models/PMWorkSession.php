<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PMWorkSession extends Model
{
    protected $table = 'pm_work_sessions';

    protected $fillable = [
        'pm_schedule_id',
        'actual_date',
        'start_time',
        'end_time',
        'duration',
    ];

    public function pmSchedule()
    {
        return $this->belongsTo(PMSchedule::class, 'pm_schedule_id');
    }
}
