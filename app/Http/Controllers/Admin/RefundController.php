<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function index(): View
    {
        $payments = Payment::query()
            ->with(['appointment.patient', 'appointment.doctor', 'appointment.department'])
            ->whereIn('status', [Payment::STATUS_PAID, Payment::STATUS_REFUNDED])
            ->latest('id')
            ->paginate(15);

        $stats = [
            'collected'     => (float) Payment::where('status', Payment::STATUS_PAID)->sum('amount'),
            'refundedTotal' => (float) Payment::sum('refund_amount'),
            'refundedCount' => Payment::where('status', Payment::STATUS_REFUNDED)->count(),
        ];

        return view('admin.refunds', compact('payments', 'stats'));
    }

    public function refund(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->status === Payment::STATUS_PAID, 422, 'Only paid payments can be refunded.');

        $remaining = round((float) $payment->amount - (float) $payment->refund_amount, 2);

        abort_if($remaining <= 0, 422, 'This payment is already fully refunded.');

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$remaining],
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $payment->refund_amount = min(round((float) $payment->refund_amount + (float) $data['amount'], 2), (float) $payment->amount);
        $payment->refund_reason = $data['reason'];
        $payment->refunded_at = now();

        if ($payment->refund_amount >= (float) $payment->amount) {
            $payment->status = Payment::STATUS_REFUNDED;
        }

        $payment->save();

        return back()->with('status', 'Refund processed successfully.');
    }
}
