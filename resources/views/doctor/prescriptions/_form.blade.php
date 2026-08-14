@php
    $prescription ??= null;
    $appointment ??= $prescription?->appointment;
    $patient = $appointment?->patient ?? $prescription?->patient;
    $method ??= null;

    $existingRows = $prescription
        ? $prescription->items->map(fn ($item) => [
            'medication_name' => $item->medication_name,
            'dosage' => $item->dosage,
            'frequency' => $item->frequency,
            'duration' => $item->duration,
            'instructions' => $item->instructions,
        ])->values()->all()
        : [];
@endphp

@if ($patient?->medicalProfile && ($patient->medicalProfile->allergies || $patient->medicalProfile->chronic_conditions))
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Patient alerts</p>
        <div class="mt-2 flex flex-wrap gap-2">
            @foreach (array_merge($patient->medicalProfile->allergies ?? [], $patient->medicalProfile->chronic_conditions ?? []) as $alert)
                <span class="badge bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-200">{{ $alert }}</span>
            @endforeach
        </div>
    </div>
@endif

<form method="POST" action="{{ $action }}" x-data="prescriptionForm"
      @if ($prescription) x-init="rows = {{ Js::from($existingRows) }}" @endif
      class="card p-6">
    @csrf
    @if ($method)
        @method($method)
    @endif
    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-4">
            <div>
                <label for="diagnosis" class="label">Diagnosis <span class="text-red-500">*</span></label>
                <textarea id="diagnosis" name="diagnosis" rows="2" class="input mt-1" required>{{ old('diagnosis', $prescription?->diagnosis) }}</textarea>
                @error('diagnosis')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="investigation" class="label">Investigations</label>
                <textarea id="investigation" name="investigation" rows="2" class="input mt-1">{{ old('investigation', $prescription?->investigation) }}</textarea>
            </div>

            <div>
                <label for="follow_up_date" class="label">Follow-up date</label>
                <input id="follow_up_date" name="follow_up_date" type="date" min="{{ now()->addDay()->toDateString() }}"
                       value="{{ old('follow_up_date', $prescription?->follow_up_date?->toDateString()) }}" class="input mt-1">
                @error('follow_up_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label for="dietary_advice" class="label">Dietary advice</label>
                <textarea id="dietary_advice" name="dietary_advice" rows="2" class="input mt-1">{{ old('dietary_advice', $prescription?->dietary_advice) }}</textarea>
            </div>

            <div>
                <label for="doctor_notes" class="label">Notes</label>
                <textarea id="doctor_notes" name="doctor_notes" rows="2" class="input mt-1">{{ old('doctor_notes', $prescription?->doctor_notes) }}</textarea>
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
                <div class="rounded-xl border border-brand-100 p-3">
                    <div class="grid gap-3 lg:grid-cols-12">
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
                        <div class="lg:col-span-2 flex items-center justify-end">
                            <button type="button" @click="removeRow(index)" class="text-sm text-red-500 hover:text-red-700"
                                    :disabled="rows.length === 1">Remove</button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <input type="text" :name="`items[${index}][instructions]`" x-model="row.instructions"
                               placeholder="Instructions (e.g. take after food)" class="input">
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-end gap-3">
        <a href="{{ $cancelUrl }}" class="btn-outline">Cancel</a>
        <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
    </div>
</form>
