@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

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
            <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full {{ $appointment->status === 'cancelled' ? 'bg-rose-500/10 text-rose-600' : 'bg-emerald-500/10 text-emerald-600' }}">
                {{ ucfirst($appointment->status) }}
            </span>
        </div>

        @if ($appointment->status !== 'cancelled')
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
                        <i class="fa-solid fa-calendar-days mr-1 text-brand-600"></i> Reschedule (FR-06)
                    </a>
                @endif

                @if ($appointment->payment)
                    <a href="{{ route('payments.receipt', $appointment->payment) }}" class="bg-brand-50 text-brand-700 px-5 py-2.5 rounded-xl font-medium hover:bg-brand-100 transition text-sm">
                        <i class="fa-solid fa-receipt mr-1"></i> View Receipt (FR-08)
                    </a>
                @endif
            @endif

            <a href="{{ route('appointments.create') }}" class="text-muted hover:text-ink px-5 py-2.5 rounded-xl font-medium transition text-sm">
                Book Another
            </a>
        </div>

    </div>

</div>
@endsection