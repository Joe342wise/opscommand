<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escalation extends Model
{
    use Auditable, UsesArchivedSoftDeletes;

    protected $fillable = [
        'activity_id',
        'incident_id',
        'owner_id',
        'target_team_id',
        'created_by',
        'updated_by',
        'reason',
        'priority',
        'status',
        'archived_at',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function targetTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'target_team_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(EscalationHistory::class);
    }
}
