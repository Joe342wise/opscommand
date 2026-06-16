<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_returns_standard_success_response_with_token(): void
    {
        $role = Role::create(['name' => 'Administrator', 'description' => 'Full access']);
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password1',
            'device_name' => 'test-suite',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Request completed successfully.')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['token', 'token_type', 'user' => ['id', 'name', 'email', 'role']],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'api.auth.login_success']);
    }

    public function test_api_logout_requires_sanctum_authentication(): void
    {
        $this->postJson('/api/v1/auth/logout')
            ->assertUnauthorized();
    }

    public function test_api_logout_deletes_current_token(): void
    {
        $user = User::create([
            'name' => 'Token User',
            'email' => 'token@example.com',
            'password' => Hash::make('Password1'),
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Logged out successfully.');
    }

    public function test_api_failed_login_is_rate_limited_and_audited(): void
    {
        RateLimiter::clear('api-login|limited@example.com|127.0.0.1');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'limited@example.com',
                'password' => 'wrong-password',
            ])->assertUnauthorized()
                ->assertJsonPath('success', false);
        }

        $this->postJson('/api/v1/auth/login', [
            'email' => 'limited@example.com',
            'password' => 'wrong-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertSame(5, AuditLog::where('action', 'api.auth.login_failed')->count());
    }
}
