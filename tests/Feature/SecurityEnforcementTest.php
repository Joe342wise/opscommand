<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_routes_require_assigned_permission(): void
    {
        $role = Role::create(['name' => 'Restricted', 'description' => 'No permissions']);
        $user = User::create([
            'name' => 'Restricted User',
            'email' => 'restricted@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertForbidden();
    }

    public function test_failed_login_attempts_are_rate_limited_and_audited(): void
    {
        RateLimiter::clear('limited@example.com|127.0.0.1');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login'), [
                'email' => 'limited@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => 'limited@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertSame(5, AuditLog::where('action', 'auth.login_failed')->count());
    }
}
