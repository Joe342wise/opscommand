<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Department;
use App\Models\Escalation;
use App\Models\Handover;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiEscalationHandoverTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Shift $shift;

    private Activity $activity;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Operations Lead', 'description' => 'Operations lead']);

        foreach (['escalate_incidents', 'manage_handovers'] as $permission) {
            $role->permissions()->attach(Permission::create([
                'name' => $permission,
                'description' => $permission,
                'module' => 'operations',
            ]));
        }

        $this->user = User::create([
            'name' => 'Ops Lead',
            'email' => 'ops-lead@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $department = Department::create(['name' => 'Application Support']);
        $this->team = Team::create(['name' => 'Support Team A', 'department_id' => $department->id]);
        $this->shift = Shift::create(['name' => 'Day Shift', 'start_time' => '08:00', 'end_time' => '16:00']);
        $this->activity = Activity::create([
            'title' => 'Gateway review',
            'priority' => 'high',
            'status' => 'pending',
            'owner_id' => $this->user->id,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_escalation_endpoints_create_filter_close_and_archive(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/escalations', [
            'activity_id' => $this->activity->id,
            'target_team_id' => $this->team->id,
            'reason' => 'Requires infrastructure review.',
            'priority' => 'high',
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.escalation.status', 'pending')
            ->assertJsonPath('data.escalation.target_team.name', 'Support Team A');

        $escalation = Escalation::firstOrFail();

        $this->getJson('/api/v1/escalations?status=pending&priority=high&search=infrastructure')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.reason', 'Requires infrastructure review.');

        $this->postJson("/api/v1/escalations/{$escalation->id}/close", [
            'summary' => 'Resolved by target team.',
        ])->assertOk()
            ->assertJsonPath('message', 'Escalation closed successfully.')
            ->assertJsonPath('data.escalation.status', 'resolved');

        $this->assertDatabaseHas('escalation_histories', [
            'escalation_id' => $escalation->id,
            'previous_status' => 'pending',
            'new_status' => 'resolved',
        ]);

        $this->deleteJson("/api/v1/escalations/{$escalation->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Escalation archived successfully.');

        $this->assertNotNull($escalation->fresh()->archived_at);
    }

    public function test_handover_endpoints_create_item_acknowledge_and_archive(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/handovers', [
            'shift_id' => $this->shift->id,
            'summary' => 'Day shift handover.',
            'risk_summary' => 'Monitor gateway latency.',
            'items' => [[
                'item_type' => 'activity',
                'activity_id' => $this->activity->id,
                'description' => 'Continue gateway review.',
                'priority' => 'high',
            ]],
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.handover.summary', 'Day shift handover.')
            ->assertJsonPath('data.handover.items.0.item_type', 'activity');

        $handover = Handover::firstOrFail();

        $this->postJson("/api/v1/handovers/{$handover->id}/items", [
            'item_type' => 'manual',
            'description' => 'Manual follow-up item.',
            'priority' => 'medium',
        ])->assertCreated()
            ->assertJsonPath('message', 'Handover item added successfully.');

        $this->postJson("/api/v1/handovers/{$handover->id}/acknowledge")
            ->assertOk()
            ->assertJsonPath('message', 'Handover acknowledged successfully.')
            ->assertJsonPath('data.handover.status', 'acknowledged');

        $this->assertDatabaseHas('handover_acknowledgements', [
            'handover_id' => $handover->id,
            'acknowledged_by' => $this->user->id,
            'status' => 'acknowledged',
        ]);

        $this->deleteJson("/api/v1/handovers/{$handover->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Handover archived successfully.');

        $this->assertNotNull($handover->fresh()->archived_at);
    }
}
