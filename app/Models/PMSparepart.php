<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PMSparepart extends Model
{
    protected $table = 'pm_spareparts';
    protected $fillable = [
        'pm_schedule_id',
        'sparepart_id',
        'qty',
    ];

    public function pmSchedule()
    {
        return $this->belongsTo(PMSchedule::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}