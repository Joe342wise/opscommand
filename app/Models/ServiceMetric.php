<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceMetric extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'service_id',
        'created_by',
        'updated_by',
        'metric_name',
        'metric_value',
        'unit',
        'archived_at',
    ];

    protected $casts = [
        'metric_value' => 'decimal:2',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
