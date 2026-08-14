<?php

namespace Tests\Feature\Doctor;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_view_their_schedule(): void
    {
        $doctor = $this->makeDoctor();
        DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);

        $this->actingAs($doctor->user)
            ->get('/doctor/schedule')
            ->assertOk()
            ->assertSee('Request leave');
    }

    public function test_doctor_can_request_leave(): void
    {
        $doctor = $this->makeDoctor();

        $response = $this->actingAs($doctor->user)->post('/doctor/leave', [
            'date' => now()->addDays(3)->toDateString(),
            'reason' => 'Personal appointment',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('doctor_leaves', [
            'doctor_id' => $doctor->id,
            'reason' => 'Personal appointment',
        ]);
    }

    public function test_leave_date_must_be_in_the_future(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($doctor->user)
            ->post('/doctor/leave', [
                'date' => now()->subDay()->toDateString(),
                'reason' => 'Too late',
            ])
            ->assertSessionHasErrors('date');
    }

    private function makeDoctor(): Doctor
    {
        return Doctor::factory()->create();
    }
}
