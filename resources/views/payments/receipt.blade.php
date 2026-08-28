@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-center font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-surface border border-brand-100 rounded-3xl shadow-xl p-8 printable" id="receipt-card">

        <!-- Receipt Header -->
        <div class="flex justify-between items-start border-b border-dashed border-brand-100 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-black text-ink">MediQueue</h1>
                <p class="text-xs uppercase tracking-widest text-brand-600 font-semibold mt-0.5">Official Payment Receipt</p>
            </div>
            <div class="text-right text-xs text-muted">
                <p><span class="font-semibold text-ink">Receipt #:</span> {{ $payment->receipt_number }}</p>
                <p><span class="font-semibold text-ink">Date:</span> {{ optional($payment->paid_at)->format('M d, Y h:i A') ?? now()->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="grid grid-cols-2 gap-4 bg-surface-alt rounded-2xl p-4 mb-6 text-sm">
            <div>
                <span class="text-xs text-muted block">Transaction ID</span>
                <span class="font-mono font-bold text-ink text-xs sm:text-sm">{{ $payment->transaction_id ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-xs text-muted block">Payment Method</span>
                <span class="font-semibold text-ink">{{ $payment->paymentMethodLabel() }}</span>
            </div>
        </div>

        <!-- Appointment Breakdown -->
        <div class="space-y-3 text-sm text-muted mb-6">
            <div class="flex justify-between">
                <span>Patient:</span>
                <span class="font-semibold text-ink">{{ optional($payment->appointment->patient)->name ?? 'Patient' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Doctor:</span>
                <span class="font-semibold text-ink">Dr. {{ $payment->appointment->doctor->display_name ?? optional($payment->appointment->doctor->user)->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Department:</span>
                <span class="font-semibold text-ink">{{ $payment->appointment->department->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Appointment Date &amp; Slot:</span>
                <span class="font-semibold text-ink">{{ $payment->appointment->date->format('M d, Y') }} ({{ $payment->appointment->time_slot }})</span>
            </div>
            <div class="flex justify-between">
                <span>Token Number:</span>
                <span class="font-bold text-brand-600">{{ $payment->appointment->token_number }}</span>
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="border-t border-brand-100 pt-4 space-y-2 text-sm text-muted">
            <div class="flex justify-between">
                <span>Consultation Fee</span>
                <span class="font-medium text-ink">৳{{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Hospital Service Charge</span>
                <span class="font-medium text-ink">৳{{ number_format($payment->service_fee ?? 50, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>VAT (5%)</span>
                <span class="font-medium text-ink">৳{{ number_format($payment->vat_amount ?? ($payment->amount * 0.05), 2) }}</span>
            </div>
            <div class="border-t border-brand-100 pt-3 flex justify-between text-base font-bold text-ink">
                <span>Total Amount Paid</span>
                <span class="text-brand-600">৳{{ number_format($payment->total_paid ?? ($payment->amount + 50 + ($payment->amount * 0.05)), 2) }} (PAID)</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3 no-print">
            <button onclick="window.print()" class="bg-ink text-surface px-6 py-2.5 rounded-xl font-medium hover:opacity-90 transition text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i> Print / Save as PDF
            </button>
            <a href="{{ route('appointments.show', $payment->appointment) }}" class="bg-brand-50 text-brand-700 px-6 py-2.5 rounded-xl font-medium hover:bg-brand-100 transition text-sm text-center">
                View Queue Token
            </a>
        </div>

    </div>

</div>

<style>
@media print {
    .no-print, nav, header { display: none !important; }
    body { background: white !important; }
    .printable { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>
@endsection