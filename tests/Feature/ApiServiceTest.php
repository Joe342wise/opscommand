<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Service Admin', 'description' => 'Service admin']);

        foreach (['view_dashboard', 'manage_services'] as $permission) {
            $role->permissions()->attach(Permission::create([
                'name' => $permission,
                'description' => $permission,
                'module' => 'services',
            ]));
        }

        $this->user = User::create([
            'name' => 'Service Admin',
            'email' => 'service-admin@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }

    public function test_service_endpoints_create_filter_update_metrics_and_archive(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/services', [
            'name' => 'SMS Gateway',
            'category' => 'Communication',
            'status' => 'healthy',
            'description' => 'SMS delivery gateway.',
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.service.name', 'SMS Gateway')
            ->assertJsonPath('data.service.status', 'healthy');

        $service = Service::firstOrFail();

        $this->getJson('/api/v1/services?status=healthy&category=Communication&search=SMS')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'SMS Gateway');

        $this->getJson('/api/v1/services/stats')
            ->assertOk()
            ->assertJsonPath('data.stats.total', 1)
            ->assertJsonPath('data.stats.healthy', 1);

        $this->getJson("/api/v1/services/{$service->id}")
            ->assertOk()
            ->assertJsonPath('data.service.id', $service->id);

        $this->patchJson("/api/v1/services/{$service->id}", [
            'status' => 'warning',
            'description' => 'Updated description.',
        ])->assertOk()
            ->assertJsonPath('message', 'Service updated successfully.')
            ->assertJsonPath('data.service.status', 'warning');

        $this->postJson("/api/v1/services/{$service->id}/metrics", [
            'metric_name' => 'response_time',
            'metric_value' => 245.50,
            'unit' => 'ms',
        ])->assertCreated()
            ->assertJsonPath('message', 'Metric added successfully.')
            ->assertJsonPath('data.metric.metric_name', 'response_time');

        $this->assertDatabaseHas('service_metrics', [
            'service_id' => $service->id,
            'metric_name' => 'response_time',
        ]);

        $this->deleteJson("/api/v1/services/{$service->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Service archived successfully.');

        $this->assertNotNull($service->fresh()->archived_at);
    }
}
