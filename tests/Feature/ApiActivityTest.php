<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiActivityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Administrator', 'description' => 'Full access']);

        foreach (['manage_activities', 'update_activities'] as $permission) {
            $role->permissions()->attach(Permission::create([
                'name' => $permission,
                'description' => $permission,
                'module' => 'activities',
            ]));
        }

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->owner = User::create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }

    public function test_activity_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/activities')
            ->assertUnauthorized();
    }

    public function test_activity_index_supports_filtering_and_pagination(): void
    {
        Sanctum::actingAs($this->admin);

        Activity::create([
            'title' => 'Critical Gateway Check',
            'description' => 'Gateway latency investigation',
            'priority' => 'critical',
            'status' => 'pending',
            'owner_id' => $this->owner->id,
            'created_by' => $this->admin->id,
        ]);

        Activity::create([
            'title' => 'Routine Report Review',
            'priority' => 'low',
            'status' => 'completed',
            'owner_id' => $this->owner->id,
            'created_by' => $this->admin->id,
        ]);

        $this->getJson('/api/v1/activities?status=pending&priority=critical&search=gateway&per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'Critical Gateway Check');
    }

    public function test_activity_store_validates_and_returns_standard_response(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/v1/activities', [
            'description' => 'Missing title',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'priority', 'owner_id']);

        $this->postJson('/api/v1/activities', [
            'title' => 'Create activity via API',
            'description' => 'API-created operational activity',
            'priority' => 'high',
            'owner_id' => $this->owner->id,
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Activity created successfully.')
            ->assertJsonPath('data.activity.title', 'Create activity via API')
            ->assertJsonPath('data.activity.status', 'pending');
    }

    public function test_activity_update_records_status_history(): void
    {
        Sanctum::actingAs($this->admin);

        $activity = Activity::create([
            'title' => 'Status transition activity',
            'priority' => 'medium',
            'status' => 'pending',
            'owner_id' => $this->owner->id,
            'created_by' => $this->admin->id,
        ]);

        $this->putJson("/api/v1/activities/{$activity->id}", [
            'status' => 'in_progress',
            'update_summary' => 'Work started.',
        ])->assertOk()
            ->assertJsonPath('data.activity.status', 'in_progress');

        $this->assertDatabaseHas('activity_updates', [
            'activity_id' => $activity->id,
            'previous_status' => 'pending',
            'new_status' => 'in_progress',
            'summary' => 'Work started.',
        ]);
    }

    public function test_activity_remarks_and_archive_are_supported(): void
    {
        Sanctum::actingAs($this->admin);

        $activity = Activity::create([
            'title' => 'Remarked activity',
            'priority' => 'medium',
            'status' => 'pending',
            'owner_id' => $this->owner->id,
            'created_by' => $this->admin->id,
        ]);

        $this->postJson("/api/v1/activities/{$activity->id}/remarks", [
            'remark' => 'Initial investigation note.',
        ])->assertCreated()
            ->assertJsonPath('message', 'Remark added successfully.');

        $this->deleteJson("/api/v1/activities/{$activity->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Activity archived successfully.');

        $this->assertNotNull($activity->fresh()->archived_at);
    }
}
