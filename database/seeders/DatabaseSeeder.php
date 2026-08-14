<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('users')->where('role', 'admin')->exists()) {
            return;
        }

        DB::transaction(function () {
            $this->call([
                DepartmentSeeder::class,
                DoctorSeeder::class,
                PatientSeeder::class,
                DoctorScheduleSeeder::class,
                AppointmentSeeder::class,
            ]);
        });
    }
}
