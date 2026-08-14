<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => User::factory()->create(['role' => 'patient']),
            'doctor_id' => Doctor::factory(),
            'department_id' => Department::factory(),
            'date' => now()->today(),
            'time_slot' => $this->faker->randomElement(['09:00', '09:30', '10:00', '10:30', '11:00']),
            'status' => Appointment::STATUS_SCHEDULED,
            'notes' => null,
        ];
    }
}
