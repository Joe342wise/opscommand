<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Administrator', 'description' => 'Admin']);
        $manageUsers = Permission::firstOrCreate(['name' => 'manage_users'], ['module' => 'users', 'description' => 'Manage users']);
        $role->permissions()->attach($manageUsers->id);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin);
    }

    public function test_users_index_page_loads(): void
    {
        $this->get(route('users.index'))->assertOk();
    }

    public function test_user_can_be_created(): void
    {
        $role = Role::create(['name' => 'Test Role', 'description' => 'Test']);

        $response = $this->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'new@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'new@test.com']);
    }

    public function test_user_can_be_viewed(): void
    {
        $user = User::first();
        $this->get(route('users.show', $user))->assertOk();
    }

    public function test_user_can_be_updated(): void
    {
        $user = User::first();

        $response = $this->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role_id' => $user->role_id,
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_user_can_be_archived(): void
    {
        $role = Role::create(['name' => 'Archivable', 'description' => 'Test']);
        $user = User::create([
            'name' => 'Archivable User',
            'email' => 'archive@test.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $response = $this->delete(route('users.destroy', $user));
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_cannot_archive_own_account(): void
    {
        $response = $this->delete(route('users.destroy', $this->admin));
        $response->assertSessionHasErrors();
    }
}
