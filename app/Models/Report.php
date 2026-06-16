<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'description',
        'parameters',
        'data',
        'status',
        'created_by',
        'updated_by',
        'archived_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'data' => 'array',
        'archived_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exports(): HasMany
    {
        return $this->hasMany(ReportExport::class);
    }
}
