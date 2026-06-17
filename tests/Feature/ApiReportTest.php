<?php

namespace Tests\Feature;

use App\Models\KpiSnapshot;
use App\Models\Permission;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Report Viewer', 'description' => 'Report viewer']);
        $role->permissions()->attach(Permission::create([
            'name' => 'view_reports',
            'description' => 'View reports',
            'module' => 'reports',
        ]));

        $this->user = User::create([
            'name' => 'Report Viewer',
            'email' => 'report-viewer@example.com',
            'password' => Hash::make('Password1'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }

    public function test_report_endpoints_generate_filter_show_and_archive(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/reports', [
            'title' => 'Weekly Activity Summary',
            'type' => 'activity',
            'description' => 'Activity summary for the week.',
            'date_from' => '2026-06-01',
            'date_to' => '2026-06-07',
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.report.title', 'Weekly Activity Summary')
            ->assertJsonPath('data.report.status', 'generated')
            ->assertJsonStructure(['data' => ['report' => ['id', 'title', 'type', 'data', 'parameters']]]);

        $report = Report::firstOrFail();

        $this->getJson('/api/v1/reports?type=activity&search=Weekly')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'Weekly Activity Summary');

        $this->getJson("/api/v1/reports/{$report->id}")
            ->assertOk()
            ->assertJsonPath('data.report.id', $report->id)
            ->assertJsonPath('data.report.parameters.date_from', '2026-06-01');

        $this->deleteJson("/api/v1/reports/{$report->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Report archived successfully.');

        $this->assertNotNull($report->fresh()->archived_at);
    }

    public function test_kpi_endpoint_returns_latest_snapshots(): void
    {
        Sanctum::actingAs($this->user);

        KpiSnapshot::create([
            'kpi_name' => 'mttr',
            'value' => 45.00,
            'unit' => 'minutes',
            'snapshot_date' => '2026-06-15',
        ]);
        KpiSnapshot::create([
            'kpi_name' => 'sla_compliance',
            'value' => 98.50,
            'unit' => 'percent',
            'snapshot_date' => '2026-06-15',
        ]);
        KpiSnapshot::create([
            'kpi_name' => 'mttr',
            'value' => 50.00,
            'unit' => 'minutes',
            'snapshot_date' => '2026-06-14',
        ]);

        $this->getJson('/api/v1/reports/kpis?sort=snapshot_date&direction=desc')
            ->assertOk()
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('latest_kpis.mttr', '45.00')
            ->assertJsonPath('latest_kpis.sla_compliance', '98.50');

        $this->getJson('/api/v1/reports/kpis?kpi_name=mttr')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }
}
