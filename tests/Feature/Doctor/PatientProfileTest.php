<?php

namespace Tests\Feature\Doctor;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_treating_doctor_can_view_patient_profile(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);

        $response = $this->actingAs($doctor->user)->get("/doctor/patients/{$patient->id}");

        $response->assertOk();
        $response->assertSee($patient->name);
    }

    public function test_doctor_with_prescription_can_view_patient_profile(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        Prescription::factory()->create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
        ]);

        $this->actingAs($doctor->user)->get("/doctor/patients/{$patient->id}")->assertOk();
    }

    public function test_unrelated_doctor_cannot_view_patient_profile(): void
    {
        $doctor = Doctor::factory()->create();
        $other = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        Appointment::factory()->create([
            'doctor_id' => $other->id,
            'department_id' => $other->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);

        $this->actingAs($doctor->user)->get("/doctor/patients/{$patient->id}")->assertForbidden();
    }

    public function test_doctor_cannot_view_a_non_patient_profile(): void
    {
        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();

        $this->actingAs($doctor->user)
            ->get("/doctor/patients/{$otherDoctor->user->id}")
            ->assertNotFound();
    }

    public function test_patient_cannot_access_patient_profile_view(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
        ]);

        $this->actingAs($patient)->get("/doctor/patients/{$patient->id}")->assertForbidden();
    }
}
