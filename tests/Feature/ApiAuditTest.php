<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\HistoricalRecord;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Audit Viewer', 'description' => 'Audit viewer']);
        $role->permissions()->attach(Permission::create([
            'name' => 'view_audit_logs',
            'description' => 'View audit logs',
            'module' => 'audit',
        ]));

        $this->user = User::create([
            'name' => 'Audit Viewer',
            'email' => 'audit-viewer@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }

    public function test_audit_log_endpoints_list_filter_and_show(): void
    {
        Sanctum::actingAs($this->user);

        AuditLog::create([
            'actor_id' => $this->user->id,
            'action' => 'login',
            'entity_type' => 'user',
            'entity_id' => $this->user->id,
            'ip_address' => '127.0.0.1',
        ]);
        AuditLog::create([
            'actor_id' => $this->user->id,
            'action' => 'create',
            'entity_type' => 'activity',
            'entity_id' => 1,
            'new_values' => ['title' => 'Test activity'],
        ]);

        $this->getJson('/api/v1/audit-logs?action=login&entity_type=user')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.action', 'login')
            ->assertJsonPath('data.0.actor.name', 'Audit Viewer');

        $log = AuditLog::firstOrFail();

        $this->getJson("/api/v1/audit-logs/{$log->id}")
            ->assertOk()
            ->assertJsonPath('data.audit_log.id', $log->id)
            ->assertJsonPath('data.audit_log.actor.name', 'Audit Viewer');
    }

    public function test_history_endpoint_filters_and_returns_records(): void
    {
        Sanctum::actingAs($this->user);

        HistoricalRecord::create([
            'entity_type' => 'activity',
            'entity_id' => 1,
            'action' => 'status_change',
            'changes' => ['status' => ['old' => 'pending', 'new' => 'completed']],
            'created_by' => $this->user->id,
        ]);
        HistoricalRecord::create([
            'entity_type' => 'incident',
            'entity_id' => 2,
            'action' => 'created',
            'changes' => ['title' => 'New incident'],
            'created_by' => $this->user->id,
        ]);

        $this->getJson('/api/v1/audit-logs/history?entity_type=activity&action=status_change')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.action', 'status_change')
            ->assertJsonPath('data.0.changes.status.old', 'pending');

        $this->getJson('/api/v1/audit-logs/history')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }
}
