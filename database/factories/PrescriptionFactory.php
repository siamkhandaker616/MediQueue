<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory()->state(['status' => Appointment::STATUS_COMPLETED]),
            'doctor_id' => fn (array $attrs) => Appointment::find($attrs['appointment_id'])->doctor_id,
            'patient_id' => fn (array $attrs) => Appointment::find($attrs['appointment_id'])->patient_id,
            'diagnosis' => $this->faker->sentence(),
            'is_editable' => true,
        ];
    }
}
