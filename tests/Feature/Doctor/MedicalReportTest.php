<?php

namespace Tests\Feature\Doctor;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_treating_doctor_sees_patients_reports_on_profile(): void
    {
        Storage::fake('public');
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        $report = MedicalReport::factory()->create([
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
        ]);

        $this->actingAs($doctor->user)
            ->get("/doctor/patients/{$patient->id}")
            ->assertOk()
            ->assertSee($report->file_name);
    }

    public function test_treating_doctor_can_download_report(): void
    {
        Storage::fake('public');
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        $report = MedicalReport::factory()->create([
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
        ]);
        Storage::disk('public')->put($report->file_path, 'fake-report-content');

        $this->actingAs($doctor->user)
            ->get("/doctor/patients/{$patient->id}/reports/{$report->id}")
            ->assertOk()
            ->assertDownload($report->file_name);
    }

    public function test_unrelated_doctor_cannot_download_report(): void
    {
        Storage::fake('public');
        $doctor = Doctor::factory()->create();
        $other = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $other->id,
            'department_id' => $other->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        $report = MedicalReport::factory()->create([
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
        ]);

        $this->actingAs($doctor->user)
            ->get("/doctor/patients/{$patient->id}/reports/{$report->id}")
            ->assertForbidden();
    }

    public function test_doctor_cannot_download_another_patients_report_via_url_mismatch(): void
    {
        Storage::fake('public');
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $otherPatient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        $report = MedicalReport::factory()->create([
            'patient_id' => $otherPatient->id,
            'appointment_id' => $appointment->id,
        ]);

        $this->actingAs($doctor->user)
            ->get("/doctor/patients/{$patient->id}/reports/{$report->id}")
            ->assertNotFound();
    }
}
