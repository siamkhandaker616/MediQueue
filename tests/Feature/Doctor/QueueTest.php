<?php

namespace Tests\Feature\Doctor;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_view_their_today_queue(): void
    {
        $doctor = $this->makeDoctor();
        $patient = User::factory()->create(['role' => 'patient']);
        Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
            'date' => now()->today(),
            'status' => Appointment::STATUS_CHECKED_IN,
        ]);

        $response = $this->actingAs($doctor->user)->get('/doctor/queue');

        $response->assertOk();
        $response->assertSee($patient->name);
    }

    public function test_doctor_can_advance_appointment_status(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->today(),
            'status' => Appointment::STATUS_CHECKED_IN,
        ]);

        $response = $this->actingAs($doctor->user)
            ->patch("/doctor/queue/{$appointment->id}/status", ['status' => Appointment::STATUS_IN_PROGRESS]);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_doctor_cannot_touch_another_doctors_appointment(): void
    {
        $doctor = $this->makeDoctor();
        $other = $this->makeDoctor();
        $appointment = Appointment::factory()->create([
            'doctor_id' => $other->id,
            'department_id' => $other->department_id,
        ]);

        $this->actingAs($doctor->user)
            ->patch("/doctor/queue/{$appointment->id}/status", ['status' => Appointment::STATUS_IN_PROGRESS])
            ->assertForbidden();
    }

    public function test_doctor_can_mark_a_patient_as_no_show(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->today(),
            'status' => Appointment::STATUS_CHECKED_IN,
        ]);

        $this->actingAs($doctor->user)
            ->patch("/doctor/queue/{$appointment->id}/status", ['status' => Appointment::STATUS_NO_SHOW])
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_NO_SHOW,
        ]);
    }

    public function test_doctor_cannot_set_an_invalid_status(): void
    {
        $doctor = $this->makeDoctor();
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->today(),
        ]);

        $this->actingAs($doctor->user)
            ->patch("/doctor/queue/{$appointment->id}/status", ['status' => 'nonsense'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
    }

    public function test_patient_cannot_access_doctor_queue(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $this->actingAs($patient)->get('/doctor/queue')->assertForbidden();
    }

    public function test_guest_is_redirected_from_doctor_queue(): void
    {
        $this->get('/doctor/queue')->assertRedirect(route('login'));
    }

    private function makeDoctor(): Doctor
    {
        return Doctor::factory()->create();
    }
}
