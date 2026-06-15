<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'created_by',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'due_at',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ActivityUpdate::class);
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(ActivityRemark::class);
    }

    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'activity_incident');
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(Escalation::class);
    }
}
