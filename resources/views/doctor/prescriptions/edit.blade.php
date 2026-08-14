<x-layouts.staff>
    <x-slot name="title">Edit prescription</x-slot>

    @php
        $appointment = $prescription->appointment;
        $patient = $prescription->patient;
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-14 · issued {{ $prescription->created_at->format('d M Y, g:i a') }}</p>
            <h1 class="text-2xl font-bold tracking-tight">Edit prescription</h1>
            <p class="mt-1 text-xs text-muted">Editable until {{ $prescription->created_at->addMinutes((int) config('mediqueue.prescription_edit_grace_minutes'))->format('g:i a') }}.</p>
        </div>
        <a href="{{ route('doctor.prescriptions.show', $prescription) }}" class="btn-outline">Back to prescription</a>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Patient</p>
            <p class="mt-1 font-semibold">{{ $patient->name }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Token</p>
            <p class="mt-1 font-mono text-sm font-semibold text-brand-700">{{ $appointment->token_number }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Date</p>
            <p class="mt-1 font-semibold">{{ $appointment->date->format('d M Y') }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Time</p>
            <p class="mt-1 font-semibold">{{ $appointment->time_slot }}</p>
        </div>
    </div>

    @include('doctor.prescriptions._form', [
        'prescription' => $prescription,
        'action' => route('doctor.prescriptions.update', $prescription),
        'method' => 'PATCH',
        'submitLabel' => 'Update prescription',
        'cancelUrl' => route('doctor.prescriptions.show', $prescription),
    ])
</x-layouts.staff>
