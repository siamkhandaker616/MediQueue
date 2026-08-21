<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_paying_patient_can_download_their_receipt(): void
    {
        $payment = Payment::factory()->create();

        $response = $this->actingAs($payment->appointment->patient)
            ->get("/payments/{$payment->id}/receipt");

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_admin_can_download_any_receipt(): void
    {
        $payment = Payment::factory()->create();

        $response = $this->actingAs($this->makeAdmin())
            ->get("/payments/{$payment->id}/receipt");

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_another_patient_cannot_download_someone_elses_receipt(): void
    {
        $payment = Payment::factory()->create();
        $stranger = User::factory()->create(['role' => 'patient']);

        $this->actingAs($stranger)
            ->get("/payments/{$payment->id}/receipt")
            ->assertForbidden();
    }

    public function test_doctor_cannot_download_receipts(): void
    {
        $payment = Payment::factory()->create();
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor->user)
            ->get("/payments/{$payment->id}/receipt")
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_receipt(): void
    {
        $payment = Payment::factory()->create();

        $this->get("/payments/{$payment->id}/receipt")
            ->assertRedirect(route('login'));
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }
}
