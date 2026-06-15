<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MfaVerification extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'secret', 'code', 'complete_at', 'expires_at'];

    protected function casts(): array
    {
        return [
            'complete_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
