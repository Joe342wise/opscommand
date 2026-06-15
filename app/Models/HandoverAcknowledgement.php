<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandoverAcknowledgement extends Model
{
    protected $fillable = [
        'handover_id',
        'acknowledged_by',
        'status',
    ];

    public function handover(): BelongsTo
    {
        return $this->belongsTo(Handover::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }
}
