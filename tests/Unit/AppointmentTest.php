<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_number_uses_department_prefix_and_padded_id(): void
    {
        $department = Department::factory()->create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $appointment = Appointment::factory()->create(['department_id' => $department->id]);

        $this->assertSame(
            'CARD-'.str_pad((string) $appointment->id, 4, '0', STR_PAD_LEFT),
            $appointment->token_number,
        );
    }

    public function test_queue_position_counts_patients_ahead_on_same_day(): void
    {
        $doctor = Doctor::factory()->create();

        Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->today(),
            'time_slot' => '09:00',
            'status' => Appointment::STATUS_CHECKED_IN,
        ]);

        $mine = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->today(),
            'time_slot' => '10:00',
            'status' => Appointment::STATUS_SCHEDULED,
        ]);

        $this->assertSame(2, $mine->queue_position);
    }

    public function test_completed_appointments_are_not_counted_ahead(): void
    {
        $doctor = Doctor::factory()->create();

        Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->today(),
            'time_slot' => '09:00',
            'status' => Appointment::STATUS_COMPLETED,
        ]);

        $mine = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->today(),
            'time_slot' => '10:00',
            'status' => Appointment::STATUS_SCHEDULED,
        ]);

        $this->assertSame(1, $mine->queue_position);
    }
}
