<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorLeave;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorLeave>
 */
class DoctorLeaveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'date' => now()->addDays($this->faker->numberBetween(1, 30)),
            'reason' => $this->faker->sentence(),
        ];
    }
}
