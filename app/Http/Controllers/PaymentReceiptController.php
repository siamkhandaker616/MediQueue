<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PaymentReceiptController extends Controller
{
    /**
     * FR-08: Downloadable digital payment receipt.
     * Accessible to the paying patient and to hospital admins.
     */
    public function show(Payment $payment): Response
    {
        $user = auth()->user();

        abort_unless(
            $user->isAdmin() || $payment->appointment->patient_id === $user->id,
            403,
        );

        abort_unless(in_array($payment->status, [Payment::STATUS_PAID, Payment::STATUS_REFUNDED], true), 404);

        $payment->load(['appointment.patient', 'appointment.doctor', 'appointment.department']);

        return Pdf::loadView('receipts.payment', ['payment' => $payment])
            ->download('receipt-'.$payment->receipt_number.'.pdf');
    }
}
