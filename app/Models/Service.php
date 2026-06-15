<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'category',
        'status',
        'description',
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
