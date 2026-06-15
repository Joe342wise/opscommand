<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricalRecord extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'changes',
        'created_by',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
