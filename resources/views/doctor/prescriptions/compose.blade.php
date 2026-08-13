<x-layouts.staff>
    <x-slot name="title">New prescription</x-slot>

    @php
        $patient = $appointment->patient;
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-14 · digital prescription</p>
            <h1 class="text-2xl font-bold tracking-tight">New prescription</h1>
        </div>
        <a href="{{ route('doctor.queue') }}" class="btn-outline">Back to queue</a>
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

    @if ($patient->medicalProfile && ($patient->medicalProfile->allergies || $patient->medicalProfile->chronic_conditions))
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Patient alerts</p>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach (array_merge($patient->medicalProfile->allergies ?? [], $patient->medicalProfile->chronic_conditions ?? []) as $alert)
                    <span class="badge bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-200">{{ $alert }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('doctor.prescriptions.store') }}" x-data="prescriptionForm"
          class="card p-6">
        @csrf
        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="space-y-4">
                <div>
                    <label for="diagnosis" class="label">Diagnosis <span class="text-red-500">*</span></label>
                    <textarea id="diagnosis" name="diagnosis" rows="2" class="input mt-1" required>{{ old('diagnosis') }}</textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="investigation" class="label">Investigations</label>
                    <textarea id="investigation" name="investigation" rows="2" class="input mt-1">{{ old('investigation') }}</textarea>
                </div>

                <div>
                    <label for="follow_up_date" class="label">Follow-up date</label>
                    <input id="follow_up_date" name="follow_up_date" type="date" min="{{ now()->addDay()->toDateString() }}"
                           value="{{ old('follow_up_date') }}" class="input mt-1">
                    @error('follow_up_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="dietary_advice" class="label">Dietary advice</label>
                    <textarea id="dietary_advice" name="dietary_advice" rows="2" class="input mt-1">{{ old('dietary_advice') }}</textarea>
                </div>

                <div>
                    <label for="doctor_notes" class="label">Notes</label>
                    <textarea id="doctor_notes" name="doctor_notes" rows="2" class="input mt-1">{{ old('doctor_notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Medications</h2>
                <button type="button" @click="addRow()" class="btn-outline !px-3 !py-1.5 !text-xs">+ Add medication</button>
            </div>

            <div class="mt-4 space-y-3">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="grid gap-3 rounded-xl border border-brand-100 p-3 lg:grid-cols-12">
                        <div class="lg:col-span-3">
                            <input type="text" :name="`items[${index}][medication_name]`" x-model="row.medication_name"
                                   placeholder="Medication name" class="input" required>
                        </div>
                        <div class="lg:col-span-2">
                            <input type="text" :name="`items[${index}][dosage]`" x-model="row.dosage"
                                   placeholder="Dosage" class="input" required>
                        </div>
                        <div class="lg:col-span-3">
                            <input type="text" :name="`items[${index}][frequency]`" x-model="row.frequency"
                                   placeholder="Frequency" class="input" required>
                        </div>
                        <div class="lg:col-span-2">
                            <input type="text" :name="`items[${index}][duration]`" x-model="row.duration"
                                   placeholder="Duration" class="input">
                        </div>
                        <div class="lg:col-span-1 flex items-center justify-end">
                            <button type="button" @click="removeRow(index)" class="text-sm text-red-500 hover:text-red-700"
                                    :disabled="rows.length === 1">Remove</button>
                        </div>
                    </div>
                </template>
            </div>
            <p class="mt-2 text-xs text-muted">Instructions can be added after saving, or in the print view.</p>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3">
            <a href="{{ route('doctor.queue') }}" class="btn-outline">Cancel</a>
            <button type="submit" class="btn-primary">Save prescription</button>
        </div>
    </form>
</x-layouts.staff>
