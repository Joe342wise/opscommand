<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Permission;
use App\Models\Personnel;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PersonnelManagementTest extends TestCase
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

    public function test_personnel_index_page_loads(): void
    {
        $this->get(route('personnel.index'))->assertOk();
    }

    public function test_personnel_can_be_created(): void
    {
        $department = Department::create(['name' => 'Test Dept', 'created_by' => 1]);
        $team = Team::create(['name' => 'Test Team', 'department_id' => $department->id, 'created_by' => 1]);

        $response = $this->post(route('personnel.store'), [
            'name' => 'John Doe',
            'position' => 'Engineer',
            'team_id' => $team->id,
            'status' => 'active',
            'availability' => 'available',
            'email' => 'john@test.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('personnel', ['name' => 'John Doe']);
    }

    public function test_personnel_can_be_viewed(): void
    {
        $department = Department::create(['name' => 'Test Dept', 'created_by' => 1]);
        $team = Team::create(['name' => 'Test Team', 'department_id' => $department->id, 'created_by' => 1]);
        $person = Personnel::create([
            'name' => 'Jane Doe',
            'position' => 'Manager',
            'team_id' => $team->id,
            'status' => 'active',
            'availability' => 'available',
            'email' => 'jane@test.com',
        ]);

        $this->get(route('personnel.show', $person))->assertOk();
    }

    public function test_personnel_can_be_updated(): void
    {
        $department = Department::create(['name' => 'Test Dept', 'created_by' => 1]);
        $team = Team::create(['name' => 'Test Team', 'department_id' => $department->id, 'created_by' => 1]);
        $person = Personnel::create([
            'name' => 'Jane Doe',
            'position' => 'Manager',
            'team_id' => $team->id,
            'status' => 'active',
            'availability' => 'available',
            'email' => 'jane@test.com',
        ]);

        $response = $this->put(route('personnel.update', $person), [
            'name' => 'Jane Smith',
            'position' => 'Senior Manager',
            'team_id' => $team->id,
            'status' => 'active',
            'availability' => 'on_leave',
            'email' => 'jane@test.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('personnel', ['id' => $person->id, 'name' => 'Jane Smith']);
    }
}
