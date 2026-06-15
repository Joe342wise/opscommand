<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'Administrator')->first();

        User::updateOrCreate(
            ['email' => 'nanayawosei429@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin342'),
                'role_id' => $role?->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
