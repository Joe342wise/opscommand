<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Handover extends Model
{
    use Auditable;
    protected $fillable = [
        'shift_id',
        'created_by',
        'summary',
        'risk_summary',
        'status',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(HandoverItem::class);
    }

    public function acknowledgements(): HasMany
    {
        return $this->hasMany(HandoverAcknowledgement::class);
    }
}
