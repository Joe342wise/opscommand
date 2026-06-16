<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestigationNote extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'incident_id',
        'created_by',
        'updated_by',
        'note',
        'archived_at',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
