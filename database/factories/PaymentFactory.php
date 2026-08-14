<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'amount' => 800,
            'method' => $this->faker->randomElement(['bkash', 'nagad', 'card']),
            'transaction_id' => strtoupper($this->faker->bothify('TXN####??##')),
            'gateway_response' => null,
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ];
    }
}
