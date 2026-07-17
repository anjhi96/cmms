<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PMChecklist extends Model
{
    protected $table = 'pm_checklists';
    protected $fillable = [

        'pm_schedule_id',

        'machine_checklist_id',

        'clean',

        'lubrication',

        'replace',

        'check',

        'remarks',

    ];

    public function pmSchedule()
    {
        return $this->belongsTo(PMSchedule::class);
    }

    public function machineChecklist()
    {
        return $this->belongsTo(MachineChecklist::class);
    }
}