<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandoverAcknowledgement extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'handover_id',
        'acknowledged_by',
        'created_by',
        'updated_by',
        'status',
        'archived_at',
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
