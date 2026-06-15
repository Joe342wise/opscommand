<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaRecord extends Model
{
    protected $fillable = [
        'service_id',
        'sla_name',
        'target_value',
        'actual_value',
        'unit',
        'is_met',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'is_met' => 'boolean',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
