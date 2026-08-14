<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'doctor']),
            'department_id' => Department::factory(),
            'name' => 'Dr. '.$this->faker->name(),
            'email' => fn (array $attrs) => User::find($attrs['user_id'])?->email,
            'qualifications' => 'MBBS, FCPS',
            'specialties' => [$this->faker->word()],
            'experience_years' => $this->faker->numberBetween(3, 20),
            'consultation_fee' => $this->faker->numberBetween(500, 1500),
            'languages' => ['Bengali', 'English'],
            'bio' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
