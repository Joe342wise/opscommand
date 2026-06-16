<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResolutionRecord extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'incident_id',
        'created_by',
        'updated_by',
        'resolved_by',
        'summary',
        'root_cause',
        'corrective_action',
        'preventive_action',
        'archived_at',
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
