<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PMSchedule extends Model
{
    protected $table = 'pm_schedules';
    protected $fillable = [
        'machine_id',
        'machine_number',
        'machine_type',
        'area',
        'order_number',
        'plan_date',
        'plan_month',
        'plan_year',
        'due_date',
        'last_pm',
        'pic',
        'actual_date',
        'start_time',
        'end_time',
        'duration',
        'oil_change',
        'greasing',
        'wo_zsbp',
        'remarks',
        'next_pm',
        'status',
    ];

    public function model(array $row)
    {
        return new PMSchedule([
            'machine_number' => $row['machine_number'],
            'plan_date' => $row['plan_date'],
            'plan_month' => $row['plan_month'],
            'plan_year' => $row['plan_year'],
            'status' => $row['status'],
        ]);
    }

    protected function picFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->pic
                ? \Illuminate\Support\Str::title(strtolower($this->pic))
                : '-'
        );
    }

    public function requiresOilChange(): bool
    {
        return in_array($this->machine_type, [
            'NDE',
            'NDB',
            'BFM',
        ]);
    }

    public function getDurationFormattedAttribute()
    {
        $totalDuration = $this->duration;

        if (!$totalDuration && $this->relationLoaded('workSessions')) {
            $totalDuration = $this->workSessions->sum('duration');
        }

        if (!$totalDuration) {
            return '';
        }

        $hours = floor($totalDuration / 60);
        $minutes = $totalDuration % 60;

        return "{$hours} Hours {$minutes} Minutes";
    }

    public function getTotalDurationAttribute()
    {
        if ($this->relationLoaded('workSessions')) {
            return $this->workSessions->sum('duration');
        }

        return $this->duration ?? 0;
    }

    public function getTotalDurationFormattedAttribute()
    {
        $totalDuration = $this->totalDuration;

        if (!$totalDuration) {
            return '';
        }

        $hours = floor($totalDuration / 60);
        $minutes = $totalDuration % 60;

        return "{$hours} Hours {$minutes} Minutes";
    }

    public function getTotalManHourAttribute()
    {
        if ($this->relationLoaded('workSessions')) {
            return $this->workSessions->sum(function ($session) {
                return $session->manpowers->sum('man_hour');
            });
        }

        return 0;
    }

    public function getTotalManHourFormattedAttribute()
    {
        $totalManHour = $this->totalManHour;

        if (!$totalManHour) {
            return '';
        }

        $hours = floor($totalManHour / 60);
        $minutes = $totalManHour % 60;
        $decimal = number_format($totalManHour / 60, 2);

        return "{$decimal} MH ({$hours} Hours {$minutes} Minutes)";
    }

    public function workSessions()
    {
        return $this->hasMany(PMWorkSession::class, 'pm_schedule_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function measurements()
    {
        return $this->hasMany(
            PMMeasurement::class,
            'pm_schedule_id'
        );
    }


    public function problems()
    {
        return $this->hasMany(
            PMProblem::class,
            'pm_schedule_id'
        );
    }

    public function spareparts()
    {
        return $this->hasMany(
            PMSparepart::class,
            'pm_schedule_id'
        );
    }

    public function checklists()
    {
        return $this->hasMany(
            PMChecklist::class,
            'pm_schedule_id'
        );
    }
}
