<?php

namespace App\Models;

use App\Traits\UsesArchivedSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use UsesArchivedSoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'status',
        'description',
        'created_by',
        'updated_by',
        'archived_at',
    ];

    public function metrics(): HasMany
    {
        return $this->hasMany(ServiceMetric::class);
    }

    public function slaRecords(): HasMany
    {
        return $this->hasMany(SlaRecord::class);
    }
}
