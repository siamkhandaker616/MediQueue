<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\MedicalReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalReport>
 */
class MedicalReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => fn (array $attrs) => Appointment::find($attrs['appointment_id'])->patient_id,
            'appointment_id' => Appointment::factory()->state(['status' => Appointment::STATUS_COMPLETED]),
            'file_path' => 'reports/' . $this->faker->uuid() . '.pdf',
            'file_name' => $this->faker->word() . '-report.pdf',
            'file_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(100, 5000),
            'report_type' => $this->faker->randomElement(['blood_test', 'xray', 'mri', 'ultrasound', 'general']),
            'report_date' => $this->faker->date(),
        ];
    }
}
