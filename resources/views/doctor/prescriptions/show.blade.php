<x-layouts.staff>
    <x-slot name="title">Prescription #{{ $prescription->id }}</x-slot>

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-15 · issued {{ $prescription->created_at->format('d M Y, g:i a') }}</p>
            <h1 class="text-2xl font-bold tracking-tight">Prescription</h1>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="btn-primary">Print / PDF</button>
            <a href="{{ route('doctor.prescriptions.index') }}" class="btn-outline">History</a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-brand-100 bg-brand-50/60 px-6 py-5">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 text-white">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                </span>
                <div>
                    <p class="font-bold text-brand-700">MediQueue Medical Center</p>
                    <p class="text-xs text-muted">Outpatient Department · Digital Prescription</p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-mono text-xs font-semibold text-brand-700">RX-{{ str_pad((string) $prescription->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p class="text-xs text-muted">{{ $prescription->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <div class="grid gap-6 px-6 py-6 sm:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-muted">Patient</p>
                <p class="mt-1 font-semibold">{{ $prescription->patient->name }}</p>
                <p class="text-sm text-muted">{{ $prescription->patient->email }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-muted">Doctor</p>
                <p class="mt-1 font-semibold">{{ $prescription->doctor->name }}</p>
                <p class="text-sm text-muted">{{ $prescription->doctor->qualifications }}</p>
            </div>
        </div>

        <div class="border-t border-brand-100 px-6 py-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Diagnosis</p>
            <p class="mt-1 text-sm">{{ $prescription->diagnosis }}</p>

            @if ($prescription->investigation)
                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-muted">Investigations</p>
                <p class="mt-1 text-sm">{{ $prescription->investigation }}</p>
            @endif
        </div>

        <div class="border-t border-brand-100 px-6 py-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Medications</p>
            <table class="mt-3 w-full">
                <thead>
                    <tr>
                        <th class="table-head">Medication</th>
                        <th class="table-head">Dosage</th>
                        <th class="table-head">Frequency</th>
                        <th class="table-head">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-50">
                    @foreach ($prescription->items as $item)
                        <tr>
                            <td class="table-cell font-medium">{{ $item->medication_name }}</td>
                            <td class="table-cell text-muted">{{ $item->dosage }}</td>
                            <td class="table-cell text-muted">{{ $item->frequency }}</td>
                            <td class="table-cell text-muted">{{ $item->duration ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid gap-6 border-t border-brand-100 px-6 py-5 sm:grid-cols-2">
            @if ($prescription->dietary_advice)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted">Dietary advice</p>
                    <p class="mt-1 text-sm">{{ $prescription->dietary_advice }}</p>
                </div>
            @endif
            @if ($prescription->follow_up_date)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted">Follow-up</p>
                    <p class="mt-1 text-sm">{{ $prescription->follow_up_date->format('d M Y') }}</p>
                </div>
            @endif
        </div>

        @if ($prescription->doctor_notes)
            <div class="border-t border-brand-100 px-6 py-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted">Notes</p>
                <p class="mt-1 text-sm">{{ $prescription->doctor_notes }}</p>
            </div>
        @endif

        <div class="flex items-end justify-between border-t border-brand-100 px-6 py-6">
            <p class="text-xs text-muted">This is a digitally generated prescription.</p>
            <div class="text-center">
                <p class="font-script text-xl text-brand-700">{{ $prescription->doctor->name }}</p>
                <div class="mt-1 border-t border-brand-300 pt-1 text-xs text-muted">Signature &amp; stamp</div>
            </div>
        </div>
    </div>
</x-layouts.staff>
