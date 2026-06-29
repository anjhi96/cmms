<?php

namespace App\Models;

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
        'big_problem',
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

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
    public function details()
    {
        return $this->hasMany(PMDetail::class);
    }
}
