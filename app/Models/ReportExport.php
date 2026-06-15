<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExport extends Model
{
    protected $fillable = [
        'report_id',
        'format',
        'path',
        'size',
        'status',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
