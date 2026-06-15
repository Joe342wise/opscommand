<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResolutionRecord extends Model
{
    protected $fillable = [
        'incident_id',
        'resolved_by',
        'summary',
        'root_cause',
        'corrective_action',
        'preventive_action',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
