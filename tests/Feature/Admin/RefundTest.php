<?php

namespace Tests\Feature\Admin;

use App\Models\Doctor;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_refunds_page(): void
    {
        $payment = Payment::factory()->create();

        $response = $this->actingAs($this->makeAdmin())->get('/admin/refunds');

        $response->assertOk();
        $response->assertSee($payment->receipt_number);
        $response->assertSee($payment->appointment->patient->name);
    }

    public function test_admin_can_process_a_full_refund(): void
    {
        $payment = Payment::factory()->create(['amount' => 800]);

        $response = $this->actingAs($this->makeAdmin())
            ->post("/admin/payments/{$payment->id}/refund", [
                'amount' => 800,
                'reason' => 'Duplicate charge',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $payment->refresh();
        $this->assertSame(Payment::STATUS_REFUNDED, $payment->status);
        $this->assertEquals(800.0, (float) $payment->refund_amount);
        $this->assertSame('Duplicate charge', $payment->refund_reason);
        $this->assertNotNull($payment->refunded_at);
    }

    public function test_admin_can_process_a_partial_refund(): void
    {
        $payment = Payment::factory()->create(['amount' => 800]);

        $this->actingAs($this->makeAdmin())
            ->post("/admin/payments/{$payment->id}/refund", [
                'amount' => 300,
                'reason' => 'Goodwill partial refund',
            ]);

        $payment->refresh();
        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertEquals(300.0, (float) $payment->refund_amount);
    }

    public function test_refund_cannot_exceed_remaining_amount(): void
    {
        $payment = Payment::factory()->create(['amount' => 500]);

        $this->actingAs($this->makeAdmin())
            ->post("/admin/payments/{$payment->id}/refund", [
                'amount' => 900,
                'reason' => 'Too much',
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_refund_requires_a_reason(): void
    {
        $payment = Payment::factory()->create();

        $this->actingAs($this->makeAdmin())
            ->post("/admin/payments/{$payment->id}/refund", [
                'amount' => 100,
            ])
            ->assertSessionHasErrors('reason');
    }

    public function test_fully_refunded_payment_cannot_be_refunded_again(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 800,
            'status' => Payment::STATUS_REFUNDED,
            'refund_amount' => 800,
            'refund_reason' => 'Already refunded',
            'refunded_at' => now(),
        ]);

        $this->actingAs($this->makeAdmin())
            ->post("/admin/payments/{$payment->id}/refund", [
                'amount' => 100,
                'reason' => 'Second refund',
            ])
            ->assertStatus(422);
    }

    public function test_doctor_cannot_access_refund_management(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor->user)->get('/admin/refunds')->assertForbidden();
    }

    public function test_patient_cannot_access_refund_management(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $this->actingAs($patient)->get('/admin/refunds')->assertForbidden();
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }
}
