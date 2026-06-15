<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscalationHistory extends Model
{
    protected $fillable = [
        'escalation_id',
        'changed_by',
        'previous_status',
        'new_status',
        'summary',
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
