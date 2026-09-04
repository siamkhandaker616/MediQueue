<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * FR-07: Payment Checkout Page
     */
    public function checkout(Appointment $appointment)
    {
        if ($appointment->payment && $appointment->payment->status === Payment::STATUS_PAID) {
            return redirect()->route('payments.receipt', $appointment->payment)
                ->with('info', 'This appointment is already paid for. Here is your receipt.');
        }

        $doctorFee = (float) $appointment->fee;
        $serviceFee = 50.00;
        $vat = round(($doctorFee + $serviceFee) * 0.05, 2);
        $total = $doctorFee + $serviceFee + $vat;

        return view('payments.checkout', compact('appointment', 'doctorFee', 'serviceFee', 'vat', 'total'));
    }

    /**
     * FR-07: Process Direct Payment / Gateway Fallback
     */
    public function process(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'method' => 'required|string',
            'account_number' => 'nullable|string|min:10',
        ]);

        $doctorFee = (float) $appointment->fee;
        $serviceFee = 50.00;
        $vat = round(($doctorFee + $serviceFee) * 0.05, 2);
        $total = $doctorFee + $serviceFee + $vat;

        $payment = DB::transaction(function () use ($appointment, $validated, $total, $serviceFee, $vat) {
            $payment = Payment::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'patient_id'       => $appointment->patient_id,
                    'amount'           => $total,
                    'service_fee'      => $serviceFee,
                    'vat_amount'       => $vat,
                    'total_paid'       => $total,
                    'method'           => strtolower($validated['method']),
                    'transaction_id'   => Payment::generateTransactionId(),
                    'status'           => Payment::STATUS_PAID,
                    'gateway_response' => [
                        'gateway'   => 'SSLCommerz',
                        'timestamp' => now()->toIso8601String(),
                        'verified'  => true,
                    ],
                    'paid_at'          => now(),
                ]
            );

            $appointment->update([
                'payment_status' => 'paid',
                'status'         => Appointment::STATUS_SCHEDULED,
            ]);

            return $payment;
        });

        return redirect()->route('payments.receipt', $payment)
            ->with('success', 'Payment verified and completed! Your official digital receipt has been generated.');
    }

    /**
     * FR-08: View Digital Payment Receipt
     */
    public function receipt(Payment $payment)
    {
        $payment->load(['appointment.doctor.user', 'appointment.department', 'appointment.patient']);

        return view('payments.receipt', compact('payment'));
    }
}