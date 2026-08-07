<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PMManpower extends Model
{
    protected $table = 'pm_manpowers';

    protected $fillable = [
        'pm_work_session_id',
        'person',
        'start_time',
        'end_time',
        'duration',
        'man_hour',
    ];

    public function workSession()
    {
        return $this->belongsTo(PMWorkSession::class, 'pm_work_session_id');
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

    public function getManHourFormattedAttribute()
    {
        if (!$this->man_hour) {
            return '';
        }

        $hours = floor($this->man_hour / 60);
        $minutes = $this->man_hour % 60;
        $decimal = number_format($this->man_hour / 60, 2);

        return "{$decimal} MH ({$hours} Hours {$minutes} Minutes)";
    }
}
