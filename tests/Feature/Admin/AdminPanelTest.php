<?php

namespace Tests\Feature\Admin;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_pending_reviews(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $review = Review::factory()->create(['is_visible' => false]);

        $response = $this->actingAs($admin)->get('/admin/reviews');

        $response->assertOk();
        $response->assertSee($review->patient->name);
        $response->assertSee($review->comment);
    }

    public function test_admin_can_approve_a_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $review = Review::factory()->create(['is_visible' => false]);

        $this->actingAs($admin)
            ->patch("/admin/reviews/{$review->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_visible' => true]);
    }

    public function test_admin_can_hide_a_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $review = Review::factory()->create(['is_visible' => true]);

        $this->actingAs($admin)
            ->patch("/admin/reviews/{$review->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_visible' => false]);
    }

    public function test_doctor_cannot_access_review_moderation(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor->user)->get('/admin/reviews')->assertForbidden();
    }
}

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_analytics_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $department = Department::factory()->create();
        $doctor = Doctor::factory()->create(['department_id' => $department->id]);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        Payment::factory()->create([
            'appointment_id' => $appointment->id,
            'amount' => 800,
            'status' => Payment::STATUS_PAID,
        ]);

        $response = $this->actingAs($admin)->get('/admin/analytics');

        $response->assertOk();
        $response->assertSee('Hospital overview');
        $response->assertSee('Doctor performance');
    }
}

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_delete_departments(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/admin/departments', [
            'name' => 'Nephrology',
            'fee_range' => '900 - 1200',
            'description' => 'Kidney care',
        ])->assertRedirect();

        $this->assertDatabaseHas('departments', ['name' => 'Nephrology']);

        $department = Department::where('name', 'Nephrology')->firstOrFail();

        $this->actingAs($admin)
            ->delete("/admin/departments/{$department->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_department_with_doctors_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = Doctor::factory()->create();
        $department = $doctor->department;

        $this->actingAs($admin)
            ->delete("/admin/departments/{$department->id}")
            ->assertStatus(422);

        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }
}

class DoctorManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_doctor_active_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = Doctor::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->patch("/admin/doctors/{$doctor->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('doctors', ['id' => $doctor->id, 'is_active' => false]);
    }
}
