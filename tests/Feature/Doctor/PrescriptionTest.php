<?php

namespace Tests\Feature\Doctor;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_view_prescription_history(): void
    {
        $doctor = $this->makeDoctor();
        $patient = User::factory()->create(['role' => 'patient']);
        Prescription::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'diagnosis' => 'Seasonal flu',
        ]);

        $response = $this->actingAs($doctor->user)->get('/doctor/prescriptions');

        $response->assertOk();
        $response->assertSee('Seasonal flu');
        $response->assertSee($patient->name);
    }

    public function test_doctor_can_compose_and_save_a_prescription(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'status' => Appointment::STATUS_IN_PROGRESS,
        ]);

        $response = $this->actingAs($doctor->user)->post('/doctor/prescriptions', [
            'appointment_id' => $appointment->id,
            'diagnosis' => 'Upper respiratory infection',
            'investigation' => 'Complete blood count',
            'follow_up_date' => now()->addDays(14)->toDateString(),
            'items' => [
                [
                    'medication_name' => 'Paracetamol 500mg',
                    'dosage' => '500 mg',
                    'frequency' => '1 tablet every 6 hours',
                    'duration' => '5 days',
                ],
                [
                    'medication_name' => 'Amoxicillin 500mg',
                    'dosage' => '500 mg',
                    'frequency' => '1 capsule every 8 hours',
                    'duration' => '7 days',
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('prescriptions', ['diagnosis' => 'Upper respiratory infection']);
        $prescription = Prescription::where('diagnosis', 'Upper respiratory infection')->firstOrFail();
        $this->assertCount(2, $prescription->items);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
    }

    public function test_prescription_requires_at_least_one_medication(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'status' => Appointment::STATUS_IN_PROGRESS,
        ]);

        $response = $this->actingAs($doctor->user)->post('/doctor/prescriptions', [
            'appointment_id' => $appointment->id,
            'diagnosis' => 'Checkup',
            'items' => [],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('prescriptions', 0);
    }

    public function test_doctor_cannot_compose_for_another_doctors_appointment(): void
    {
        $doctor = $this->makeDoctor();
        $other = $this->makeDoctor();
        $appointment = Appointment::factory()->create([
            'doctor_id' => $other->id,
            'department_id' => $other->department_id,
            'status' => Appointment::STATUS_IN_PROGRESS,
        ]);

        $this->actingAs($doctor->user)
            ->get("/doctor/prescriptions/create/{$appointment->id}")
            ->assertForbidden();
    }

    private function makeDoctor(): Doctor
    {
        return Doctor::factory()->create();
    }
}
