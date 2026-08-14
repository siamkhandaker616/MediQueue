<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        $appointment = Appointment::factory()->state(['status' => Appointment::STATUS_COMPLETED]);

        return [
            'appointment_id' => $appointment,
            'patient_id' => fn (array $attrs) => Appointment::find($attrs['appointment_id'])->patient_id,
            'doctor_id' => fn (array $attrs) => Appointment::find($attrs['appointment_id'])->doctor_id,
            'punctuality_rating' => $this->faker->numberBetween(1, 5),
            'communication_rating' => $this->faker->numberBetween(1, 5),
            'knowledge_rating' => $this->faker->numberBetween(1, 5),
            'overall_rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(),
            'is_visible' => false,
        ];
    }
}
