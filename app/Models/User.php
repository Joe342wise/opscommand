<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'last_login_at',
        'mfa_enabled',
        'mfa_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'mfa_enabled' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function mfaVerifications(): HasMany
    {
        return $this->hasMany(MfaVerification::class);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role?->permissions->contains('name', $permission) ?? false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return $this->role?->permissions->whereIn('name', $permissions)->isNotEmpty() ?? false;
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'Administrator';
    }
}
