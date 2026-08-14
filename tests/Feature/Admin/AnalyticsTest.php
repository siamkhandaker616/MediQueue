<?php

namespace Tests\Feature\Admin;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
