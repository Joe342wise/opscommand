<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscalationHistory extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'escalation_id',
        'created_by',
        'updated_by',
        'changed_by',
        'previous_status',
        'new_status',
        'summary',
        'archived_at',
    ];

    public function escalation(): BelongsTo
    {
        return $this->belongsTo(Escalation::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
