<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceMetric extends Model
{
    protected $fillable = [
        'service_id',
        'metric_name',
        'metric_value',
        'unit',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
