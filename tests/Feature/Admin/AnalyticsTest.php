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

    public function test_analytics_can_be_filtered_by_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $department = Department::factory()->create();
        $doctor = Doctor::factory()->create(['department_id' => $department->id]);

        $recent = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        Payment::factory()->create([
            'appointment_id' => $recent->id,
            'amount' => 800,
            'status' => Payment::STATUS_PAID,
        ]);

        $old = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'date' => now()->subDays(30),
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        Payment::factory()->create([
            'appointment_id' => $old->id,
            'amount' => 500,
            'paid_at' => now()->subDays(30),
            'status' => Payment::STATUS_PAID,
        ]);

        $this->actingAs($admin)->get('/admin/analytics')
            ->assertOk()
            ->assertSee('৳800')
            ->assertDontSee('৳1,300');

        $from = now()->subDays(31)->toDateString();
        $to = now()->subDays(29)->toDateString();

        $this->actingAs($admin)->get("/admin/analytics?range=custom&from={$from}&to={$to}")
            ->assertOk()
            ->assertSee('৳500')
            ->assertDontSee('৳800');
    }

    public function test_admin_can_see_revenue_by_method(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $department = Department::factory()->create();
        $doctor = Doctor::factory()->create(['department_id' => $department->id]);

        foreach ([['bkash', 800], ['card', 400]] as [$method, $amount]) {
            $appointment = Appointment::factory()->create([
                'doctor_id' => $doctor->id,
                'department_id' => $department->id,
                'status' => Appointment::STATUS_COMPLETED,
            ]);
            Payment::factory()->create([
                'appointment_id' => $appointment->id,
                'amount' => $amount,
                'method' => $method,
                'status' => Payment::STATUS_PAID,
            ]);
        }

        $this->actingAs($admin)->get('/admin/analytics')
            ->assertOk()
            ->assertSee('Bkash')
            ->assertSee('Card');
    }

    public function test_admin_can_see_refund_statistics(): void
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
            'status' => Payment::STATUS_REFUNDED,
            'refund_amount' => 600,
            'refunded_at' => now(),
        ]);

        $this->actingAs($admin)->get('/admin/analytics')
            ->assertOk()
            ->assertSee('Refunds')
            ->assertSee('৳600');
    }

    public function test_admin_can_export_payments_as_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $department = Department::factory()->create();
        $doctor = Doctor::factory()->create(['department_id' => $department->id]);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        $payment = Payment::factory()->create([
            'appointment_id' => $appointment->id,
            'amount' => 800,
            'method' => 'bkash',
            'status' => Payment::STATUS_PAID,
        ]);

        $response = $this->actingAs($admin)->get('/admin/analytics/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString($payment->receipt_number, $content);
        $this->assertStringContainsString('bkash', $content);
        $this->assertStringContainsString('800.00', $content);
    }

    public function test_admin_can_view_printable_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/analytics/print')
            ->assertOk()
            ->assertSee('Analytics report');
    }
}
