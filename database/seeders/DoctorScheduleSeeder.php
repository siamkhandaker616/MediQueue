<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Weekdays 0 (Sunday) .. 4 (Thursday); two shifts per day.
        $shifts = [
            ['day_of_week' => 0, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 0, 'start_time' => '17:00', 'end_time' => '20:00'],
            ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 2, 'start_time' => '17:00', 'end_time' => '20:00'],
            ['day_of_week' => 3, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 4, 'start_time' => '09:00', 'end_time' => '13:00'],
            ['day_of_week' => 4, 'start_time' => '17:00', 'end_time' => '20:00'],
        ];

        foreach (Doctor::all() as $doctor) {
            foreach ($shifts as $shift) {
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $shift['day_of_week'],
                        'start_time' => $shift['start_time'],
                    ],
                    $shift + [
                        'doctor_id' => $doctor->id,
                        'slot_duration' => 30,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
