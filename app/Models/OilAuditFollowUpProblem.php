<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OilAuditFollowUpProblem extends Model
{
    protected $fillable = [
        'oil_audit_follow_up_id',
        'problem',
    ];

    public function followUp(): BelongsTo
    {
        return $this->belongsTo(OilAuditFollowUp::class, 'oil_audit_follow_up_id');
    }
}
