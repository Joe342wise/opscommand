<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandoverItem extends Model
{
    protected $fillable = [
        'handover_id',
        'activity_id',
        'incident_id',
        'escalation_id',
        'item_type',
        'description',
        'priority',
    ];

    public function handover(): BelongsTo
    {
        return $this->belongsTo(Handover::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function escalation(): BelongsTo
    {
        return $this->belongsTo(Escalation::class);
    }
}
