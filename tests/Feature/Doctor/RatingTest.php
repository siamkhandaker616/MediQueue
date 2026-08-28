<?php

namespace Tests\Feature\Doctor;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_sees_own_published_ratings_breakdown(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        Review::factory()->create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'overall_rating' => 5,
            'punctuality_rating' => 4,
            'communication_rating' => 5,
            'knowledge_rating' => 4,
            'is_visible' => true,
        ]);

        $this->actingAs($doctor->user)
            ->get(route('doctor.ratings.index'))
            ->assertOk()
            ->assertSee('5.0')
            ->assertSee('Reviews');
    }

    public function test_doctor_only_sees_published_reviews(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        Review::factory()->create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'overall_rating' => 1,
            'is_visible' => false,
        ]);

        $this->actingAs($doctor->user)
            ->get(route('doctor.ratings.index'))
            ->assertOk()
            ->assertSee('No published reviews yet.');
    }

    public function test_doctor_cannot_see_another_doctors_ratings(): void
    {
        $doctor = Doctor::factory()->create();
        $other = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $other->id,
            'department_id' => $other->department_id,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        Review::factory()->create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'doctor_id' => $other->id,
            'overall_rating' => 5,
            'is_visible' => true,
        ]);

        $this->actingAs($doctor->user)
            ->get(route('doctor.ratings.index'))
            ->assertOk()
            ->assertSee('No published reviews yet.');
    }

    public function test_patient_cannot_access_doctor_ratings(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['role' => 'patient']);

        $this->actingAs($patient)
            ->get(route('doctor.ratings.index'))
            ->assertForbidden();
    }
}
