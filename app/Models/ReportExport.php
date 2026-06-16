<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExport extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'report_id',
        'created_by',
        'updated_by',
        'format',
        'path',
        'size',
        'status',
        'archived_at',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
