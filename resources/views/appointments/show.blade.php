@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10" x-data="{ cancelModal: false, cancelReason: 'Schedule conflict / Personal emergency' }">

    @if (session('success'))
        <div class="mb-6 bg-brand-50 border border-brand-200 text-brand-700 p-4 rounded-2xl text-center font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="mb-6 bg-amber-500/10 border border-amber-500/20 text-amber-700 p-4 rounded-2xl text-center font-medium">
            {{ session('info') }}
        </div>
    @endif

    <!-- Digital Token Card -->
    <div class="bg-surface rounded-3xl border border-brand-100 shadow-xl overflow-hidden text-center p-8 relative">
        
        <!-- Hospital Header -->
        <div class="border-b border-dashed border-brand-100 pb-6 mb-6">
            <h2 class="text-2xl font-black text-ink tracking-tight">MediQueue</h2>
            <p class="text-xs uppercase tracking-widest text-brand-600 font-semibold mt-1">Outpatient Queue Token</p>
        </div>

        <!-- Big Token Number -->
        <div class="my-6">
            <span class="text-xs font-semibold text-muted uppercase tracking-wider">Your Token Number</span>
            <div class="text-4xl md:text-5xl font-black text-brand-600 tracking-wider mt-1">
                {{ $appointment->token_number }}
            </div>
            <span class="inline-block mt-2 px-3 py-1 text-xs font-bold rounded-full {{ $appointment->status === 'cancelled' ? 'bg-rose-500/10 text-rose-600' : 'bg-emerald-500/10 text-emerald-600' }}">
                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
            </span>
        </div>

        <!-- If Cancelled: Show Refund & Cancellation Banner (FR-10) -->
        @if ($appointment->status === 'cancelled')
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-6 text-left my-6 space-y-2 text-sm">
                <div class="flex items-center gap-2 text-rose-700 font-bold text-base">
                    <i class="fa-solid fa-ban"></i> Appointment Cancelled
                </div>
                <p class="text-xs text-muted"><strong>Reason:</strong> {{ $appointment->cancellation_reason ?? 'Cancelled by patient' }}</p>
                @if ($appointment->payment)
                    <div class="mt-3 pt-3 border-t border-rose-500/20 flex justify-between items-center text-xs">
                        <span class="text-muted">Refund Amount:</span>
                        <span class="font-bold text-rose-700">৳{{ number_format($appointment->payment->refund_amount ?? $appointment->payment->total_paid, 2) }} ({{ strtoupper($appointment->payment->status) }})</span>
                    </div>
                @endif
            </div>
        @else
            <!-- Live Queue Metrics -->
            <div class="grid grid-cols-2 gap-4 bg-surface-alt rounded-2xl p-4 my-6">
                <div>
                    <span class="text-xs text-muted block">Queue Position</span>
                    <span class="text-2xl font-bold text-ink">#{{ $appointment->queue_position }}</span>
                </div>
                <div>
                    <span class="text-xs text-muted block">Est. Wait Time</span>
                    <span class="text-2xl font-bold text-brand-600">~{{ $appointment->estimated_wait_minutes }} mins</span>
                </div>
            </div>
        @endif

        <!-- Appointment Details -->
        <div class="space-y-3 text-left text-sm text-muted bg-surface-alt rounded-2xl p-6">
            <div class="flex justify-between">
                <span>Doctor:</span>
                <span class="font-semibold text-ink">Dr. {{ $appointment->doctor->display_name ?? $appointment->doctor->user->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Department:</span>
                <span class="font-semibold text-ink">{{ $appointment->department->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Location:</span>
                <span class="font-semibold text-ink">{{ $appointment->department->locationLabel() }}</span>
            </div>
            <div class="flex justify-between">
                <span>Date &amp; Slot:</span>
                <span class="font-semibold text-ink">{{ $appointment->date->format('M d, Y') }} ({{ $appointment->time_slot }})</span>
            </div>
            <div class="flex justify-between">
                <span>Consultation Fee:</span>
                <span class="font-bold text-brand-600">৳{{ number_format($appointment->fee, 0) }} ({{ ucfirst($appointment->payment_status) }})</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-wrap gap-3 justify-center">
            @if ($appointment->status !== 'cancelled')
                <button onclick="window.print()" class="bg-ink text-surface px-5 py-2.5 rounded-xl font-medium hover:opacity-90 transition text-sm">
                    <i class="fa-solid fa-print mr-1"></i> Print Token
                </button>

                @if ($appointment->canBeRescheduled())
                    <a href="{{ route('appointments.reschedule', $appointment) }}" class="bg-surface border border-brand-200 text-ink px-5 py-2.5 rounded-xl font-medium hover:border-brand-500 transition text-sm">
                        <i class="fa-solid fa-calendar-days mr-1 text-brand-600"></i> Reschedule
                    </a>
                @endif

                @if ($appointment->payment)
                    <a href="{{ route('payments.receipt', $appointment->payment) }}" class="bg-brand-50 text-brand-700 px-5 py-2.5 rounded-xl font-medium hover:bg-brand-100 transition text-sm">
                        <i class="fa-solid fa-receipt mr-1"></i> View Receipt
                    </a>
                @endif

                @if ($appointment->canBeCancelled())
                    <button @click="cancelModal = true" class="bg-surface border border-rose-200 text-rose-600 hover:bg-rose-50 px-5 py-2.5 rounded-xl font-medium transition text-sm">
                        <i class="fa-solid fa-ban mr-1"></i> Cancel Appointment
                    </button>
                @endif
            @endif

            <a href="{{ route('appointments.create') }}" class="text-muted hover:text-ink px-5 py-2.5 rounded-xl font-medium transition text-sm">
                Book Another
            </a>
        </div>

    </div>

    <!-- FR-10: Cancellation Modal -->
    <div x-show="cancelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="cancelModal = false" class="bg-surface border border-brand-100 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl">
            <div class="flex items-center gap-3 mb-4 text-rose-600">
                <span class="w-10 h-10 rounded-full bg-rose-500/10 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
                <h3 class="text-xl font-bold text-ink">Cancel Appointment?</h3>
            </div>

            <p class="text-xs text-muted mb-6">Are you sure you want to cancel your consultation with <strong>Dr. {{ $appointment->doctor->display_name ?? $appointment->doctor->user->name }}</strong>? Your slot will be released and refund processed.</p>

            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                @csrf

                <div class="space-y-4 text-sm mb-6">
                    <div>
                        <label class="block font-semibold text-ink mb-1 text-xs">Select Cancellation Reason</label>
                        <select name="cancellation_reason" x-model="cancelReason" class="w-full rounded-xl border-brand-200 bg-surface text-ink p-3 text-xs">
                            <option value="Schedule conflict / Personal emergency">Schedule conflict / Personal emergency</option>
                            <option value="Booked wrong doctor or department">Booked wrong doctor or department</option>
                            <option value="Symptoms resolved / Consultation no longer needed">Symptoms resolved / Consultation no longer needed</option>
                            <option value="Transportation / Location issue">Transportation / Location issue</option>
                            <option value="Other reason">Other reason</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1 text-xs">Additional Notes (Optional)</label>
                        <textarea name="cancellation_notes" rows="2" placeholder="Briefly describe if necessary..." class="w-full rounded-xl border-brand-200 bg-surface text-ink p-3 text-xs"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="cancelModal = false" class="px-4 py-2.5 rounded-xl border border-brand-200 text-muted hover:text-ink text-xs font-semibold">Keep Booking</button>
                    <button type="submit" class="bg-rose-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-rose-700 text-xs transition shadow-sm">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection