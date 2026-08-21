<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_booking(): void
    {
        $this->get(route('appointments.create'))
            ->assertRedirect(route('login'));

        $this->post('/appointments', [])
            ->assertRedirect(route('login'));
    }

    public function test_doctor_cannot_access_booking(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor->user)
            ->get(route('appointments.create'))
            ->assertForbidden();
    }

    public function test_patient_can_book_and_receives_token_and_payment(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $date = now()->addDay()->toDateString();

        $response = $this->actingAs($patient)->post('/appointments', [
            'doctor_id' => $doctor->id,
            'date'      => $date,
            'time_slot' => '09:00 - 09:30',
            'symptoms'  => 'Headache',
        ]);

        $appointment = Appointment::where('patient_id', $patient->id)->first();

        $this->assertNotNull($appointment);
        $response->assertRedirect(route('appointments.show', $appointment));
        $this->assertSame(Appointment::STATUS_SCHEDULED, $appointment->status);
        $this->assertStringStartsWith('TK-', $appointment->token_number);
        $this->assertEquals(1, $appointment->queue_position);

        $this->assertDatabaseHas('payments', [
            'appointment_id' => $appointment->id,
            'status'         => Payment::STATUS_PAID,
            'amount'         => $appointment->fee,
        ]);
    }

    public function test_slot_cannot_be_double_booked(): void
    {
        $doctor = Doctor::factory()->create();
        $date = now()->addDay()->toDateString();
        $payload = [
            'doctor_id' => $doctor->id,
            'date'      => $date,
            'time_slot' => '10:00 - 10:30',
        ];

        $first = User::factory()->create(['role' => 'patient']);
        $this->actingAs($first)->post('/appointments', $payload);

        $second = User::factory()->create(['role' => 'patient']);
        $this->actingAs($second)
            ->post('/appointments', $payload)
            ->assertSessionHasErrors('time_slot');

        $this->assertSame(1, Appointment::where('doctor_id', $doctor->id)->count());
    }

    public function test_patient_cannot_view_another_patients_token_card(): void
    {
        $appointment = Appointment::factory()->create([
            'token_number' => 'TK-TEST-001',
        ]);
        $stranger = User::factory()->create(['role' => 'patient']);

        $this->actingAs($stranger)
            ->get(route('appointments.show', $appointment))
            ->assertForbidden();
    }
}
