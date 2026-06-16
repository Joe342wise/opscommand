<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaRecord extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'service_id',
        'created_by',
        'updated_by',
        'sla_name',
        'target_value',
        'actual_value',
        'unit',
        'is_met',
        'period_start',
        'period_end',
        'archived_at',
    ];

    protected $casts = [
        'is_met' => 'boolean',
        'target_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
