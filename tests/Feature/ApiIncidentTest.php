<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiIncidentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Incident Manager', 'description' => 'Manages incidents']);
        $role->permissions()->attach(Permission::create([
            'name' => 'manage_incidents',
            'description' => 'Manage incidents',
            'module' => 'incidents',
        ]));

        $this->admin = User::create([
            'name' => 'Incident Admin',
            'email' => 'incident-admin@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->owner = User::create([
            'name' => 'Incident Owner',
            'email' => 'incident-owner@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->service = Service::create([
            'name' => 'API Gateway',
            'category' => 'Core',
            'status' => 'healthy',
        ]);
    }

    public function test_incident_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/incidents')
            ->assertUnauthorized();
    }

    public function test_incident_index_supports_filtering_and_pagination(): void
    {
        Sanctum::actingAs($this->admin);

        Incident::create([
            'title' => 'Gateway outage',
            'description' => 'API Gateway is unavailable',
            'severity' => 'P1',
            'priority' => 'critical',
            'status' => 'open',
            'owner_id' => $this->owner->id,
            'created_by' => $this->admin->id,
            'service_id' => $this->service->id,
        ]);

        Incident::create([
            'title' => 'Report delay',
            'severity' => 'P4',
            'priority' => 'low',
            'status' => 'closed',
            'owner_id' => $this->owner->id,
            'created_by' => $this->admin->id,
        ]);

        $this->getJson("/api/v1/incidents?status=open&severity=P1&service_id={$this->service->id}&search=gateway&per_page=1")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'Gateway outage');
    }

    public function test_incident_store_validates_and_returns_standard_response(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/v1/incidents', [
            'description' => 'Missing title',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'severity', 'priority', 'owner_id']);

        $this->postJson('/api/v1/incidents', [
            'title' => 'Create incident via API',
            'description' => 'API-created operational incident',
            'severity' => 'P2',
            'priority' => 'high',
            'owner_id' => $this->owner->id,
            'service_id' => $this->service->id,
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Incident created successfully.')
            ->assertJsonPath('data.incident.title', 'Create incident via API')
            ->assertJsonPath('data.incident.status', 'open')
            ->assertJsonPath('data.incident.service.name', 'API Gateway');
    }

    public function test_incident_update_records_status_history(): void
    {
        Sanctum::actingAs($this->admin);

        $incident = Incident::create([
            'title' => 'Status transition incident',
            'severity' => 'P3',
            'priority' => 'medium',
            'status' => 'open',
            'owner_id' => $this->owner->id,
            'created_by' => $this->admin->id,
        ]);

        $this->putJson("/api/v1/incidents/{$incident->id}", [
            'status' => 'investigating',
            'update_summary' => 'Investigation started.',
        ])->assertOk()
            ->assertJsonPath('data.incident.status', 'investigating');

        $this->assertDatabaseHas('incident_updates', [
            'incident_id' => $incident->id,
            'previous_status' => 'open',
            'new_status' => 'investigating',
            'summary' => 'Investigation started.',
        ]);
    }

    public function test_incident_notes_resolution_and_archive_are_supported(): void
    {
        Sanctum::actingAs($this->admin);

        $incident = Incident::create([
            'title' => 'Resolvable incident',
            'severity' => 'P2',
            'priority' => 'high',
            'status' => 'investigating',
            'owner_id' => $this->owner->id,
            'created_by' => $this->admin->id,
        ]);

        $this->postJson("/api/v1/incidents/{$incident->id}/notes", [
            'note' => 'Initial investigation note.',
        ])->assertCreated()
            ->assertJsonPath('message', 'Investigation note added successfully.');

        $this->postJson("/api/v1/incidents/{$incident->id}/resolve", [
            'summary' => 'Service restored.',
            'root_cause' => 'Configuration drift.',
        ])->assertOk()
            ->assertJsonPath('message', 'Incident resolved successfully.')
            ->assertJsonPath('data.incident.status', 'resolved')
            ->assertJsonPath('data.resolution.summary', 'Service restored.');

        $this->assertDatabaseHas('resolution_records', [
            'incident_id' => $incident->id,
            'summary' => 'Service restored.',
        ]);

        $this->deleteJson("/api/v1/incidents/{$incident->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Incident archived successfully.');

        $this->assertNotNull($incident->fresh()->archived_at);
    }
}
