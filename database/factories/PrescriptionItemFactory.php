<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrescriptionItem>
 */
class PrescriptionItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::factory(),
            'medication_name' => $this->faker->randomElement([
                'Paracetamol 500mg', 'Amoxicillin 500mg', 'Omeprazole 20mg', 'Cetirizine 10mg',
            ]),
            'dosage' => '500 mg',
            'frequency' => '1 tablet every 8 hours',
            'duration' => '7 days',
            'instructions' => null,
        ];
    }
}
