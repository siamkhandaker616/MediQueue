<?php

namespace Tests\Feature\Admin;

use App\Models\Doctor;
use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_doctors_page_includes_modal_leave_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = Doctor::factory()->create();
        $leave = DoctorLeave::factory()->create([
            'doctor_id' => $doctor->id,
            'reason' => 'Family emergency',
        ]);

        $this->actingAs($admin)
            ->get('/admin/doctors')
            ->assertOk()
            ->assertSee($doctor->name)
            ->assertSee('Family emergency')
            ->assertSee($leave->date->format('D, j M Y'));
    }

    public function test_admin_can_approve_a_pending_leave(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $leave = DoctorLeave::factory()->create(['doctor_id' => Doctor::factory()]);

        $this->actingAs($admin)
            ->patch("/admin/leaves/{$leave->id}/approve")
            ->assertRedirect();

        $this->assertDatabaseHas('doctor_leaves', ['id' => $leave->id, 'status' => DoctorLeave::STATUS_APPROVED]);
    }

    public function test_admin_can_reject_a_pending_leave(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $leave = DoctorLeave::factory()->create(['doctor_id' => Doctor::factory()]);

        $this->actingAs($admin)
            ->patch("/admin/leaves/{$leave->id}/reject")
            ->assertRedirect();

        $this->assertDatabaseHas('doctor_leaves', ['id' => $leave->id, 'status' => DoctorLeave::STATUS_REJECTED]);
    }

    public function test_only_approved_leave_blocks_booking_slots(): void
    {
        $doctor = Doctor::factory()->create();
        $date = now()->addDays(3);
        DoctorSchedule::factory()->create([
            'doctor_id' => $doctor->id,
            'day_of_week' => (string) $date->dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'slot_duration' => 30,
            'is_active' => true,
        ]);

        $leave = DoctorLeave::factory()->create(['doctor_id' => $doctor->id, 'date' => $date->toDateString(), 'status' => DoctorLeave::STATUS_PENDING]);

        $this->assertTrue(DoctorSchedule::getSlotsForDoctorAndDate($doctor, $date->toDateString())['available']);

        $doctor->leaves()->update(['status' => DoctorLeave::STATUS_APPROVED]);

        $this->assertDatabaseHas('doctor_leaves', ['id' => $leave->id, 'status' => DoctorLeave::STATUS_APPROVED]);

        $this->assertFalse(DoctorSchedule::getSlotsForDoctorAndDate($doctor, $date->toDateString())['available']);
    }

    public function test_doctor_cannot_approve_leave_requests(): void
    {
        $doctor = Doctor::factory()->create();
        $leave = DoctorLeave::factory()->create(['doctor_id' => Doctor::factory()]);

        $this->actingAs($doctor->user)
            ->patch("/admin/leaves/{$leave->id}/approve")
            ->assertForbidden();
    }
}