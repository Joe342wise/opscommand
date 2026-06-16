<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $commonColumns = [
        'departments' => ['archived_at'],
        'teams' => ['archived_at'],
        'personnel' => ['archived_at'],
        'shifts' => ['archived_at'],
        'activities' => ['archived_at'],
        'incidents' => ['archived_at'],
        'personnel_shifts' => ['created_by', 'updated_by', 'archived_at'],
        'activity_updates' => ['created_by', 'archived_at'],
        'activity_remarks' => ['updated_by', 'archived_at'],
        'activity_incident' => ['updated_by', 'archived_at'],
        'incident_updates' => ['created_by', 'archived_at'],
        'investigation_notes' => ['updated_by', 'archived_at'],
        'resolution_records' => ['created_by', 'updated_by', 'archived_at'],
        'escalations' => ['updated_by', 'archived_at'],
        'escalation_histories' => ['created_by', 'updated_by', 'archived_at'],
        'handovers' => ['updated_by', 'archived_at'],
        'handover_items' => ['created_by', 'updated_by', 'archived_at'],
        'handover_acknowledgements' => ['created_by', 'updated_by', 'archived_at'],
        'audit_logs' => ['created_by', 'updated_by', 'archived_at'],
        'historical_records' => ['updated_by', 'archived_at'],
        'attachments' => ['created_by', 'updated_by', 'archived_at'],
        'alerts' => ['updated_by', 'archived_at'],
        'notifications' => ['created_by', 'updated_by', 'archived_at'],
        'notification_recipients' => ['created_by', 'updated_by', 'archived_at'],
        'services' => ['created_by', 'updated_by', 'archived_at'],
        'service_metrics' => ['created_by', 'updated_by', 'archived_at'],
        'sla_records' => ['created_by', 'updated_by', 'archived_at'],
        'reports' => ['updated_by', 'archived_at'],
        'report_exports' => ['created_by', 'updated_by', 'archived_at'],
        'kpi_snapshots' => ['created_by', 'updated_by', 'archived_at'],
    ];

    private array $deletedAtTables = [
        'departments',
        'teams',
        'personnel',
        'shifts',
        'activities',
        'incidents',
    ];

    public function up(): void
    {
        foreach ($this->commonColumns as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns) {
                foreach ($columns as $column) {
                    if (! Schema::hasColumn($table, $column)) {
                        $this->addCommonColumn($blueprint, $column);
                    }
                }
            });
        }

        foreach ($this->deletedAtTables as $table) {
            if (Schema::hasColumn($table, 'deleted_at') && Schema::hasColumn($table, 'archived_at')) {
                DB::table($table)
                    ->whereNull('archived_at')
                    ->whereNotNull('deleted_at')
                    ->update(['archived_at' => DB::raw('deleted_at')]);
            }
        }

        $this->addIndex('incidents', 'service_id', 'incidents_service_id_index');
        $this->addIndex('handovers', 'shift_id', 'handovers_shift_id_index');

        $this->addForeign('incidents', 'service_id', 'services');
        $this->addForeign('escalations', 'activity_id', 'activities');
        $this->addForeign('escalations', 'incident_id', 'incidents');
        $this->addForeign('handover_items', 'activity_id', 'activities');
        $this->addForeign('handover_items', 'incident_id', 'incidents');
        $this->addForeign('handover_items', 'escalation_id', 'escalations');
    }

    public function down(): void
    {
        $this->dropForeign('handover_items', 'handover_items_escalation_id_foreign');
        $this->dropForeign('handover_items', 'handover_items_incident_id_foreign');
        $this->dropForeign('handover_items', 'handover_items_activity_id_foreign');
        $this->dropForeign('escalations', 'escalations_incident_id_foreign');
        $this->dropForeign('escalations', 'escalations_activity_id_foreign');
        $this->dropForeign('incidents', 'incidents_service_id_foreign');

        $this->dropIndex('handovers', 'handovers_shift_id_index');
        $this->dropIndex('incidents', 'incidents_service_id_index');

        foreach ($this->commonColumns as $table => $columns) {
            foreach ($columns as $column) {
                if (in_array($column, ['created_by', 'updated_by'], true)) {
                    $this->dropForeign($table, "{$table}_{$column}_foreign");
                }
            }
        }

        foreach (array_reverse($this->commonColumns) as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns) {
                foreach (array_reverse($columns) as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $blueprint->dropColumn($column);
                    }
                }
            });
        }
    }

    private function addCommonColumn(Blueprint $table, string $column): void
    {
        if (in_array($column, ['created_by', 'updated_by'], true)) {
            $table->foreignId($column)->nullable()->constrained('users')->nullOnDelete();

            return;
        }

        $table->timestamp('archived_at')->nullable();
    }

    private function addIndex(string $table, string $column, string $name): void
    {
        try {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->index($column, $name));
        } catch (Throwable) {
        }
    }

    private function dropIndex(string $table, string $name): void
    {
        try {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
        } catch (Throwable) {
        }
    }

    private function addForeign(string $table, string $column, string $references): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $blueprint) use ($column, $references) {
                $blueprint->foreign($column)->references('id')->on($references)->restrictOnDelete();
            });
        } catch (Throwable) {
        }
    }

    private function dropForeign(string $table, string $name): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        try {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropForeign($name));
        } catch (Throwable) {
        }
    }
};
