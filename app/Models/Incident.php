<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'service_id',
        'owner_id',
        'created_by',
        'title',
        'description',
        'severity',
        'priority',
        'status',
        'resolved_at',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withDefault(['name' => '—']);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_incident');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(IncidentUpdate::class);
    }

    public function investigationNotes(): HasMany
    {
        return $this->hasMany(InvestigationNote::class);
    }

    public function resolutionRecord(): HasOne
    {
        return $this->hasOne(ResolutionRecord::class);
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(Escalation::class);
    }
}
