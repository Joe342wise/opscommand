<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiSnapshot extends Model
{
    protected $fillable = [
        'kpi_name',
        'value',
        'unit',
        'snapshot_date',
        'metadata',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'metadata' => 'array',
    ];
}
