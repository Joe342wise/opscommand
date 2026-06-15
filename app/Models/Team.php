<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function personnel(): HasMany
    {
        return $this->hasMany(Personnel::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
