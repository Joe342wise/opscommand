<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'filename',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
        'created_by',
        'updated_by',
        'archived_at',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
