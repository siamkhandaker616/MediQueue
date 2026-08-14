<?php

namespace Tests\Feature\Doctor;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
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

    public function test_doctor_can_view_compose_page(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'status' => Appointment::STATUS_IN_PROGRESS,
        ]);

        $this->actingAs($doctor->user)
            ->get("/doctor/prescriptions/create/{$appointment->id}")
            ->assertOk()
            ->assertSee($appointment->patient->name);
    }

    public function test_doctor_can_view_edit_page_within_grace_period(): void
    {
        $doctor = $this->makeDoctor();
        $prescription = Prescription::factory()->create([
            'doctor_id' => $doctor->id,
            'diagnosis' => 'Seasonal flu',
        ]);
        PrescriptionItem::factory()->count(2)->create(['prescription_id' => $prescription->id]);

        $this->actingAs($doctor->user)
            ->get("/doctor/prescriptions/{$prescription->id}/edit")
            ->assertOk()
            ->assertSee('Seasonal flu')
            ->assertSee('Update prescription');
    }

    public function test_doctor_can_update_prescription_within_grace_period(): void
    {
        $doctor = $this->makeDoctor();
        $prescription = Prescription::factory()->create([
            'doctor_id' => $doctor->id,
            'diagnosis' => 'Seasonal flu',
        ]);
        PrescriptionItem::factory()->count(2)->create(['prescription_id' => $prescription->id]);

        $response = $this->actingAs($doctor->user)->patch("/doctor/prescriptions/{$prescription->id}", [
            'appointment_id' => $prescription->appointment_id,
            'diagnosis' => 'Viral fever',
            'items' => [
                [
                    'medication_name' => 'Paracetamol 500mg',
                    'dosage' => '500 mg',
                    'frequency' => '1 tablet every 6 hours',
                    'duration' => '5 days',
                    'instructions' => 'Take after food',
                ],
            ],
        ]);

        $response->assertRedirect(route('doctor.prescriptions.show', $prescription));

        $this->assertDatabaseHas('prescriptions', [
            'id' => $prescription->id,
            'diagnosis' => 'Viral fever',
        ]);
        $this->assertCount(1, $prescription->fresh()->items);
        $this->assertSame('Take after food', $prescription->fresh()->items->first()->instructions);
    }

    public function test_doctor_cannot_edit_prescription_after_grace_period(): void
    {
        $doctor = $this->makeDoctor();
        $prescription = Prescription::factory()->create([
            'doctor_id' => $doctor->id,
            'created_at' => now()->subHours(2),
        ]);

        $this->actingAs($doctor->user)
            ->get("/doctor/prescriptions/{$prescription->id}/edit")
            ->assertForbidden();

        $this->actingAs($doctor->user)
            ->patch("/doctor/prescriptions/{$prescription->id}", [
                'appointment_id' => $prescription->appointment_id,
                'diagnosis' => 'Changed',
                'items' => [
                    ['medication_name' => 'X', 'dosage' => '1', 'frequency' => 'daily'],
                ],
            ])
            ->assertForbidden();
    }

    public function test_doctor_cannot_edit_prescription_that_is_not_editable(): void
    {
        $doctor = $this->makeDoctor();
        $prescription = Prescription::factory()->create([
            'doctor_id' => $doctor->id,
            'is_editable' => false,
        ]);

        $this->actingAs($doctor->user)
            ->get("/doctor/prescriptions/{$prescription->id}/edit")
            ->assertForbidden();
    }

    public function test_doctor_cannot_edit_another_doctors_prescription(): void
    {
        $doctor = $this->makeDoctor();
        $other = $this->makeDoctor();
        $prescription = Prescription::factory()->create(['doctor_id' => $other->id]);

        $this->actingAs($doctor->user)
            ->get("/doctor/prescriptions/{$prescription->id}/edit")
            ->assertForbidden();
    }

    public function test_doctor_can_download_prescription_pdf(): void
    {
        $doctor = $this->makeDoctor();
        $prescription = Prescription::factory()->create([
            'doctor_id' => $doctor->id,
            'diagnosis' => 'Seasonal flu',
        ]);
        PrescriptionItem::factory()->count(2)->create(['prescription_id' => $prescription->id]);

        $this->actingAs($doctor->user)
            ->get("/doctor/prescriptions/{$prescription->id}/pdf")
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    private function makeDoctor(): Doctor
    {
        return Doctor::factory()->create();
    }
}
