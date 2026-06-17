<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'User', 'description' => 'Basic user']);
        $perm = Permission::firstOrCreate(['name' => 'view_dashboard'], ['module' => 'dashboard', 'description' => 'View dashboard']);
        $role->permissions()->attach($perm->id);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->actingAs($user);
    }

    public function test_profile_page_loads(): void
    {
        $this->get(route('profile.edit'))->assertOk();
    }

    public function test_profile_can_be_updated(): void
    {
        $response = $this->put(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => 'test@test.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['name' => 'Updated Name']);
    }

    public function test_password_can_be_changed(): void
    {
        $response = $this->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
    }

    public function test_wrong_current_password_fails(): void
    {
        $response = $this->put(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
    }
}
