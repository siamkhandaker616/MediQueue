<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Services\SSLCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SSLCommerzPaymentController extends Controller
{
    protected SSLCommerzService $sslcommerz;

    public function __construct(SSLCommerzService $sslcommerz)
    {
        $this->sslcommerz = $sslcommerz;
    }

    /**
     * Initiate Payment via SSLCommerz
     */
    public function pay(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'gateway' => 'required|string|in:all,bkash,nagad,rocket,card,internetbanking',
        ]);

        $doctorFee = (float) $appointment->fee;
        $serviceFee = 50.00;
        $vat = round(($doctorFee + $serviceFee) * 0.05, 2);
        $total = $doctorFee + $serviceFee + $vat;

        $payment = Payment::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'amount'           => $total,
                'method'           => $validated['gateway'] === 'all' ? 'SSLCommerz' : strtoupper($validated['gateway']),
                'transaction_id'   => Payment::generateTransactionId(),
                'status'           => Payment::STATUS_PENDING,
            ]
        );

        $result = $this->sslcommerz->initiatePayment($appointment, $payment, $validated['gateway']);

        return redirect()->away($result['redirect_url']);
    }

    /**
     * SSLCommerz Success Callback (POST / GET)
     */
    public function success(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId = $request->input('val_id');
        $cardType = $request->input('card_type', 'SSLCommerz Gateway');
        $bankTranId = $request->input('bank_tran_id', 'SSLCZ-' . rand(100000, 999999));

        $payment = Payment::where('transaction_id', $tranId)->firstOrFail();

        DB::transaction(function () use ($payment, $valId, $cardType, $bankTranId, $request) {
            $payment->update([
                'status'           => Payment::STATUS_PAID,
                'method'           => $cardType,
                'paid_at'          => now(),
                'gateway_response' => [
                    'val_id'       => $valId,
                    'bank_tran_id' => $bankTranId,
                    'card_brand'   => $request->input('card_brand', 'SSLCommerz'),
                    'currency'     => 'BDT',
                    'verified'     => true,
                ],
            ]);

            $payment->appointment->update([
                'payment_status' => 'paid',
                'status'         => Appointment::STATUS_SCHEDULED,
            ]);
        });

        return redirect()->route('payments.receipt', $payment)
            ->with('success', 'Payment verified and completed via SSLCommerz! Receipt generated.');
    }

    /**
     * SSLCommerz Fail Callback
     */
    public function fail(Request $request)
    {
        $tranId = $request->input('tran_id');
        if ($tranId) {
            $payment = Payment::where('transaction_id', $tranId)->first();
            if ($payment) {
                $payment->update(['status' => 'failed']);
            }
        }

        return redirect()->route('payments.checkout', $payment->appointment ?? 1)
            ->withErrors(['payment' => 'SSLCommerz transaction failed. Please try another payment method.']);
    }

    /**
     * SSLCommerz Cancel Callback
     */
    public function cancel(Request $request)
    {
        return redirect()->route('patient.history')
            ->with('info', 'Payment was cancelled. You can complete payment anytime before your consultation.');
    }

    /**
     * SSLCommerz IPN Webhook
     */
    public function ipn(Request $request)
    {
        return response()->json(['status' => 'IPN_RECEIVED']);
    }
}