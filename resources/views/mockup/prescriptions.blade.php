<x-layouts.mockup>
    <x-slot name="title">Prescription history</x-slot>

    @php
        $prescriptions = [
            ['id' => 'RX-2041', 'patient' => 'Rafiul Hasan', 'date' => '8 Aug 2026', 'diagnosis' => 'Acute pharyngitis', 'items' => 2],
            ['id' => 'RX-2038', 'patient' => 'Ayesha Rahman', 'date' => '5 Aug 2026', 'diagnosis' => 'Viral fever', 'items' => 2],
            ['id' => 'RX-2035', 'patient' => 'Tanvir Ahmed', 'date' => '2 Aug 2026', 'diagnosis' => 'Hypertension', 'items' => 3],
            ['id' => 'RX-2031', 'patient' => 'Nusrat Jahan', 'date' => '29 Jul 2026', 'diagnosis' => 'Migraine', 'items' => 1],
            ['id' => 'RX-2027', 'patient' => 'Karim Uddin', 'date' => '26 Jul 2026', 'diagnosis' => 'Type 2 diabetes', 'items' => 2],
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-15 | Prescription history</p>
            <h1 class="text-2xl font-bold tracking-tight">Prescriptions issued</h1>
        </div>
        <div class="flex gap-2">
            <input type="search" placeholder="Search patient..." class="input !w-56">
            <button class="btn-outline">Filter</button>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr>
                        <th class="table-head">Rx ID</th>
                        <th class="table-head">Patient</th>
                        <th class="table-head">Date</th>
                        <th class="table-head">Diagnosis</th>
                        <th class="table-head">Items</th>
                        <th class="table-head text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-50">
                    @foreach ($prescriptions as $rx)
                        <tr>
                            <td class="table-cell font-mono text-xs font-semibold text-brand-700">{{ $rx['id'] }}</td>
                            <td class="table-cell font-medium">{{ $rx['patient'] }}</td>
                            <td class="table-cell text-muted">{{ $rx['date'] }}</td>
                            <td class="table-cell text-muted">{{ $rx['diagnosis'] }}</td>
                            <td class="table-cell text-muted">{{ $rx['items'] }} meds</td>
                            <td class="table-cell text-right">
                                <button class="btn-outline !px-3 !py-1 !text-xs">View</button>
                                <button class="btn-primary ml-1 !px-3 !py-1 !text-xs">PDF</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold">Print preview</h2>
            <span class="badge bg-accent-100 text-accent-700">PDF mockup</span>
        </div>

        <div class="card mt-3 max-w-2xl p-8">
            <div class="flex items-center justify-between border-b border-brand-100 pb-4">
                <div class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-bold tracking-tight text-brand-700">MediQueue</p>
                        <p class="text-xs text-muted">Outpatient Department</p>
                    </div>
                </div>
                <p class="font-mono text-sm font-semibold text-muted">RX-2041</p>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-muted">Patient</p>
                    <p class="font-semibold">Rafiul Hasan</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Doctor</p>
                    <p class="font-semibold">Dr. Sabrina Rahman</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Date</p>
                    <p>8 Aug 2026</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Follow-up</p>
                    <p>15 Aug 2026</p>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted">Diagnosis</p>
                <p class="mt-1 text-sm">Acute pharyngitis with mild fever.</p>
            </div>

            <div class="mt-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted">Medications</p>
                <div class="mt-2 divide-y divide-brand-50 rounded-lg border border-brand-100">
                    <div class="flex justify-between px-4 py-2 text-sm">
                        <span class="font-medium">Paracetamol 500 mg</span>
                        <span class="text-muted">3x daily - 5 days - after meals</span>
                    </div>
                    <div class="flex justify-between px-4 py-2 text-sm">
                        <span class="font-medium">Azithromycin 250 mg</span>
                        <span class="text-muted">1x daily - 3 days - with food</span>
                    </div>
                </div>
            </div>

            <p class="mt-6 border-t border-dashed border-brand-200 pt-3 text-center text-[11px] text-muted">
                This prescription is valid for 30 days from issue. For any query contact the hospital.
            </p>
        </div>
    </div>
</x-layouts.mockup>
