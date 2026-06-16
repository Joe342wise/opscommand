<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityRemark extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'activity_id',
        'created_by',
        'updated_by',
        'remark',
        'archived_at',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
