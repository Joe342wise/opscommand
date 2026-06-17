<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Permission;
use App\Models\Team;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DepartmentTeamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Administrator', 'description' => 'Admin']);
        $perm = Permission::firstOrCreate(['name' => 'manage_users'], ['module' => 'users', 'description' => 'Manage users']);
        $role->permissions()->attach($perm->id);

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->actingAs($user);
    }

    public function test_department_can_be_created(): void
    {
        $response = $this->post(route('departments.store'), [
            'name' => 'Engineering',
            'description' => 'Software engineering department',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['name' => 'Engineering']);
    }

    public function test_department_can_be_updated(): void
    {
        $dept = Department::create(['name' => 'Old Name', 'created_by' => 1]);

        $response = $this->put(route('departments.update', $dept), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['id' => $dept->id, 'name' => 'New Name']);
    }

    public function test_team_can_be_created(): void
    {
        $dept = Department::create(['name' => 'Engineering', 'created_by' => 1]);

        $response = $this->post(route('teams.store'), [
            'name' => 'Backend Team',
            'department_id' => $dept->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('teams', ['name' => 'Backend Team']);
    }

    public function test_team_can_be_viewed(): void
    {
        $dept = Department::create(['name' => 'Engineering', 'created_by' => 1]);
        $team = Team::create(['name' => 'Backend Team', 'department_id' => $dept->id, 'created_by' => 1]);

        $this->get(route('teams.show', $team))->assertOk();
    }

    public function test_cannot_archive_department_with_teams(): void
    {
        $dept = Department::create(['name' => 'Has Teams', 'created_by' => 1]);
        Team::create(['name' => 'Team', 'department_id' => $dept->id, 'created_by' => 1]);

        $response = $this->delete(route('departments.destroy', $dept));
        $response->assertSessionHasErrors();
    }
}
