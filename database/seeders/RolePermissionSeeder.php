<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Administrator' => 'Full system access',
            'Operations Manager' => 'Manages operations and team activities',
            'Team Lead' => 'Leads team activities and assignments',
            'Support Personnel' => 'Handles assigned support activities',
        ];

        $permissions = [
            ['name' => 'manage_users', 'description' => 'Create, update, and manage user accounts', 'module' => 'identity'],
            ['name' => 'view_dashboard', 'description' => 'View the operations dashboard', 'module' => 'dashboard'],
            ['name' => 'manage_activities', 'description' => 'Create and manage all activities', 'module' => 'activities'],
            ['name' => 'update_activities', 'description' => 'Update status and details of assigned activities', 'module' => 'activities'],
            ['name' => 'manage_incidents', 'description' => 'Create and manage all incidents', 'module' => 'incidents'],
            ['name' => 'escalate_incidents', 'description' => 'Escalate incidents to higher authority', 'module' => 'incidents'],
            ['name' => 'manage_handovers', 'description' => 'Create and manage shift handovers', 'module' => 'handovers'],
            ['name' => 'view_reports', 'description' => 'View operational reports and analytics', 'module' => 'reports'],
            ['name' => 'view_audit_logs', 'description' => 'View system audit logs', 'module' => 'audit'],
            ['name' => 'manage_services', 'description' => 'Manage monitored services and health', 'module' => 'services'],
        ];

        $rolePermissions = [
            'Administrator' => array_column($permissions, 'name'),
            'Operations Manager' => ['view_dashboard', 'manage_activities', 'update_activities', 'manage_incidents', 'escalate_incidents', 'manage_handovers', 'view_reports', 'view_audit_logs', 'manage_services'],
            'Team Lead' => ['view_dashboard', 'manage_activities', 'update_activities', 'manage_incidents', 'escalate_incidents', 'manage_handovers', 'view_reports'],
            'Support Personnel' => ['view_dashboard', 'update_activities', 'view_reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }

        foreach ($roles as $name => $description) {
            $role = Role::updateOrCreate(['name' => $name], ['description' => $description]);
            $role->permissions()->sync(
                Permission::whereIn('name', $rolePermissions[$name])->pluck('id')
            );
        }
    }
}
