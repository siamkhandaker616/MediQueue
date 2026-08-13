<x-layouts.staff>
    <x-slot name="title">{{ $patient->name }}</x-slot>

    @php
        $allergies = $profile?->allergies ?? [];
        $conditions = $profile?->chronic_conditions ?? [];
        $medications = $profile?->current_medications ?? [];
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-12 · patient medical profile</p>
            <h1 class="text-2xl font-bold tracking-tight">{{ $patient->name }}</h1>
            <p class="mt-1 text-sm text-muted">{{ $patient->email }}</p>
        </div>
        <a href="{{ route('doctor.queue') }}" class="btn-outline">Back to queue</a>
    </div>

    @if ($profile?->blood_type || $allergies || $conditions)
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="stat-card">
                <p class="text-sm font-medium text-muted">Blood type</p>
                <p class="mt-1 text-3xl font-bold text-brand-700">{{ $profile->blood_type ?? '—' }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-medium text-muted">Allergies</p>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @forelse ($allergies as $allergy)
                        <span class="badge bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-200">{{ $allergy }}</span>
                    @empty
                        <p class="text-sm text-muted">None recorded</p>
                    @endforelse
                </div>
            </div>
            <div class="stat-card">
                <p class="text-sm font-medium text-muted">Chronic conditions</p>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @forelse ($conditions as $condition)
                        <span class="badge bg-brand-100 text-brand-700 ring-1 ring-inset ring-brand-200">{{ $condition }}</span>
                    @empty
                        <p class="text-sm text-muted">None recorded</p>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <div class="card px-5 py-4 text-sm text-muted">
            No medical profile recorded for this patient yet.
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="border-b border-brand-100 px-5 py-4">
                <h2 class="font-semibold">Visit history</h2>
            </div>
            @if ($appointments->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-muted">No visits with you.</p>
            @else
                <ul class="divide-y divide-brand-50">
                    @foreach ($appointments as $appointment)
                        <li class="flex items-center justify-between px-5 py-3">
                            <div>
                                <p class="text-sm font-medium">{{ $appointment->date->format('d M Y') }} · {{ $appointment->time_slot }}</p>
                                <p class="text-xs text-muted">{{ $appointment->department->name ?? '—' }}</p>
                            </div>
                            <span class="badge bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                {{ ucwords(str_replace('_', ' ', $appointment->status)) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card overflow-hidden">
            <div class="border-b border-brand-100 px-5 py-4">
                <h2 class="font-semibold">Medication tracker</h2>
                <p class="text-xs text-muted">FR-16 · active medications from your prescriptions</p>
            </div>

            @php
                $activeMedications = $prescriptions->flatMap->items->groupBy('medication_name');
            @endphp

            @if ($activeMedications->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-muted">No active medications.</p>
            @else
                <ul class="divide-y divide-brand-50">
                    @foreach ($activeMedications as $name => $items)
                        <li class="px-5 py-3">
                            <p class="text-sm font-medium">{{ $name }}</p>
                            <p class="text-xs text-muted">
                                {{ $items->last()->dosage }} · {{ $items->last()->frequency }}
                                @if ($items->last()->duration)
                                    · {{ $items->last()->duration }}
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-accent-600">
                                Prescribed {{ $items->last()->prescription->created_at->format('d M Y') }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-layouts.staff>
