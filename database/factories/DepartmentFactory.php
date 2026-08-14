<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'slug' => fn (array $attrs) => str()->slug($attrs['name']),
            'description' => $this->faker->sentence(),
            'fee_range' => '500 - 800',
            'floor_number' => $this->faker->numberBetween(1, 6),
            'room_number' => (string) $this->faker->numberBetween(101, 699),
            'is_active' => true,
        ];
    }
}
