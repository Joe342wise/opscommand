<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Application Support'],
            ['name' => 'Infrastructure'],
            ['name' => 'Database Administration'],
            ['name' => 'Security'],
            ['name' => 'Operations Management'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['name' => $dept['name']]);
        }
    }
}
