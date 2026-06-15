<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            ['name' => 'Morning Shift', 'start_time' => '06:00', 'end_time' => '14:00'],
            ['name' => 'Afternoon Shift', 'start_time' => '14:00', 'end_time' => '22:00'],
            ['name' => 'Night Shift', 'start_time' => '22:00', 'end_time' => '06:00'],
        ];

        foreach ($shifts as $shift) {
            Shift::updateOrCreate(
                ['name' => $shift['name']],
                ['start_time' => $shift['start_time'], 'end_time' => $shift['end_time']]
            );
        }
    }
}
