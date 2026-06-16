<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;

class KpiSnapshot extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'kpi_name',
        'value',
        'unit',
        'snapshot_date',
        'metadata',
        'archived_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'snapshot_date' => 'date',
        'metadata' => 'array',
        'archived_at' => 'datetime',
    ];
}
