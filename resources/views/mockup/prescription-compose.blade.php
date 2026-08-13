<x-layouts.mockup>
    <x-slot name="title">New prescription</x-slot>

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-14 · Digital prescription</p>
            <h1 class="text-2xl font-bold tracking-tight">New prescription</h1>
        </div>
        <div class="flex gap-2">
            <button class="btn-outline">Discard</button>
            <button class="btn-primary">Save &amp; print</button>
        </div>
    </div>

    <div class="card p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">RA</span>
                <div>
                    <p class="font-semibold">Rafiul Hasan</p>
                    <p class="text-sm text-muted">Token CRD-0052 · Appointment 10:15</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-x-8 gap-y-1 text-sm sm:grid-cols-4">
                <div>
                    <p class="text-xs text-muted">Age</p>
                    <p class="font-medium">34</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Blood group</p>
                    <p class="font-medium">B+</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Doctor</p>
                    <p class="font-medium">Dr. Sabrina Rahman</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Date</p>
                    <p class="font-medium">8 Aug 2026</p>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <div>
                <label class="label">Diagnosis</label>
                <textarea rows="3" class="input mt-1" placeholder="Clinical impression…">Acute pharyngitis with mild fever</textarea>
            </div>
            <div>
                <label class="label">Investigations / tests ordered</label>
                <textarea rows="3" class="input mt-1" placeholder="Tests, e.g. CBC, throat swab…">CBC — complete blood count</textarea>
            </div>
        </div>

        <div class="mt-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Medications</h2>
                <button @click="medications.push({ name: '', dosage: '', frequency: '', duration: '', instructions: '' })"
                        class="btn-outline !px-3 !py-1 !text-xs">+ Add medication</button>
            </div>

            <div class="mt-3 space-y-3" x-data="{ medications: [
                { name: 'Paracetamol', dosage: '500 mg', frequency: '3x daily', duration: '5 days', instructions: 'After meals' },
                { name: 'Azithromycin', dosage: '250 mg', frequency: '1x daily', duration: '3 days', instructions: 'With food' },
            ] }">
                <template x-for="(med, index) in medications" :key="index">
                    <div class="rounded-xl border border-brand-100 bg-brand-50/50 p-4">
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                            <input x-model="med.name" type="text" placeholder="Medication name" class="input">
                            <input x-model="med.dosage" type="text" placeholder="Dosage" class="input">
                            <input x-model="med.frequency" type="text" placeholder="Frequency" class="input">
                            <input x-model="med.duration" type="text" placeholder="Duration" class="input">
                            <input x-model="med.instructions" type="text" placeholder="Instructions" class="input">
                        </div>
                        <button @click="medications.splice(index, 1)" class="mt-2 text-xs font-medium text-brand-600 hover:text-brand-700">Remove</button>
                    </div>
                </template>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <label class="label">Follow-up date</label>
                <input type="date" class="input mt-1 w-auto" value="2026-08-15">
            </div>
            <p class="text-xs text-muted">Grace period: doctor may edit for 30 minutes after issuing.</p>
        </div>
    </div>
</x-layouts.mockup>
