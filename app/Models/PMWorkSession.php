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

    public function manpowers()
    {
        return $this->hasMany(PMManpower::class, 'pm_work_session_id');
    }

    public function getDurationFormattedAttribute()
    {
        if (!$this->duration) {
            return '';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        return "{$hours} Hours {$minutes} Minutes";
    }

    public function getTotalManHourAttribute()
    {
        return $this->manpowers->sum('man_hour');
    }
}
