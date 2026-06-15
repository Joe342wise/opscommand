<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $appSupport = Department::where('name', 'Application Support')->first();
        $infrastructure = Department::where('name', 'Infrastructure')->first();
        $dbAdmin = Department::where('name', 'Database Administration')->first();

        $teams = [
            ['name' => 'Support Team A', 'department_id' => $appSupport?->id],
            ['name' => 'Support Team B', 'department_id' => $appSupport?->id],
            ['name' => 'Infrastructure Team', 'department_id' => $infrastructure?->id],
            ['name' => 'Database Team', 'department_id' => $dbAdmin?->id],
        ];

        foreach ($teams as $team) {
            Team::updateOrCreate(
                ['name' => $team['name']],
                ['department_id' => $team['department_id']]
            );
        }
    }
}
