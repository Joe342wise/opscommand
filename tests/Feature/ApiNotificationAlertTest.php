<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiNotificationAlertTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $recipient;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Service Manager', 'description' => 'Service manager']);

        foreach (['view_dashboard', 'manage_services'] as $permission) {
            $role->permissions()->attach(Permission::create([
                'name' => $permission,
                'description' => $permission,
                'module' => 'notifications',
            ]));
        }

        $this->user = User::create([
            'name' => 'Service Manager',
            'email' => 'service-manager@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->recipient = User::create([
            'name' => 'Recipient',
            'email' => 'recipient@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }

    public function test_alert_endpoints_create_filter_acknowledge_resolve_and_archive(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/alerts', [
            'title' => 'Database cluster warning',
            'message' => 'Replication lag exceeded the warning threshold.',
            'severity' => 'warning',
            'entity_type' => 'service',
            'entity_id' => 10,
            'recipient_ids' => [$this->recipient->id],
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.alert.status', 'active')
            ->assertJsonPath('data.alert.notifications_count', 1);

        $alert = Alert::firstOrFail();

        $this->getJson('/api/v1/alerts?severity=warning&status=active&search=cluster')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'Database cluster warning');

        $this->postJson("/api/v1/alerts/{$alert->id}/acknowledge")
            ->assertOk()
            ->assertJsonPath('message', 'Alert acknowledged successfully.')
            ->assertJsonPath('data.alert.status', 'acknowledged');

        $this->postJson("/api/v1/alerts/{$alert->id}/resolve")
            ->assertOk()
            ->assertJsonPath('message', 'Alert resolved successfully.')
            ->assertJsonPath('data.alert.status', 'resolved');

        $this->deleteJson("/api/v1/alerts/{$alert->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Alert archived successfully.');

        $this->assertNotNull($alert->fresh()->archived_at);
    }

    public function test_notification_endpoints_filter_and_mark_read_state(): void
    {
        $alert = Alert::create([
            'title' => 'Critical API outage',
            'message' => 'API gateway is unavailable.',
            'severity' => 'critical',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);
        $notification = Notification::create([
            'alert_id' => $alert->id,
            'title' => 'Critical API outage',
            'message' => 'API gateway is unavailable.',
            'category' => 'critical',
            'created_by' => $this->user->id,
        ]);
        $recipient = NotificationRecipient::create([
            'notification_id' => $notification->id,
            'user_id' => $this->recipient->id,
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->recipient);

        $this->getJson('/api/v1/notifications?category=critical&is_read=0&search=outage')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.notification.alert.severity', 'critical');

        $this->postJson("/api/v1/notifications/{$recipient->id}/read")
            ->assertOk()
            ->assertJsonPath('message', 'Notification marked as read.')
            ->assertJsonPath('data.notification.is_read', true);

        NotificationRecipient::create([
            'notification_id' => $notification->id,
            'user_id' => $this->user->id,
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('message', 'Notifications marked as read.')
            ->assertJsonPath('data.updated_count', 1);
    }
}
