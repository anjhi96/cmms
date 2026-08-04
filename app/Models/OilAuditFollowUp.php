<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OilAuditFollowUp extends Model
{
    protected $fillable = [
        'oil_audit_id',
        'problem',
        'action_taken',
        'pic_user_id',
        'pic_name',
        'actioned_at',
    ];

    protected function casts(): array
    {
        return [
            'actioned_at' => 'datetime',
        ];
    }

    public function oilAudit(): BelongsTo
    {
        return $this->belongsTo(OilAudit::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }
}
