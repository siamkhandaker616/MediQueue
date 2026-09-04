@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6 no-print">
        <a href="{{ route('patient.prescriptions.index') }}" class="text-sm font-medium text-brand-600 hover:underline">
            &larr; Back to Prescriptions
        </a>
        <button onclick="window.print()" class="bg-ink text-surface px-5 py-2 rounded-xl text-xs font-bold hover:opacity-90 transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Print / Save as PDF
        </button>
    </div>

    <!-- Printable Official Rx Card -->
    <div class="bg-surface border border-brand-100 rounded-3xl p-8 sm:p-10 shadow-xl printable" id="prescription-document">

        <!-- Hospital & Doctor Header -->
        <div class="border-b-2 border-brand-600 pb-6 mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-black text-brand-600 tracking-tight">MediQueue Hospital</h1>
                <p class="text-xs uppercase tracking-widest text-muted font-bold mt-0.5">Outpatient Medical Department</p>
                <p class="text-xs text-muted mt-1">123 Healthcare Avenue, Medical District &bull; Tel: +880 1700-000000</p>
            </div>
            <div class="text-right">
                <h2 class="text-base font-bold text-ink">Dr. {{ $prescription->doctor->display_name ?? $prescription->doctor->user->name }}</h2>
                <p class="text-xs text-brand-600 font-semibold">{{ $prescription->doctor->qualifications }}</p>
                <p class="text-xs text-muted">{{ $prescription->doctor->department->name }}</p>
            </div>
        </div>

        <!-- Patient & Date Bar -->
        <div class="bg-surface-alt rounded-2xl p-4 mb-6 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs border border-brand-100">
            <div>
                <span class="text-muted block">Patient Name:</span>
                <span class="font-bold text-ink text-sm">{{ $prescription->patient->name }}</span>
            </div>
            <div>
                <span class="text-muted block">Prescription No:</span>
                <span class="font-mono font-bold text-brand-600">{{ $prescription->prescription_number }}</span>
            </div>
            <div>
                <span class="text-muted block">Date:</span>
                <span class="font-semibold text-ink">{{ $prescription->created_at->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-muted block">Token Number:</span>
                <span class="font-bold text-ink">{{ $prescription->appointment->token_number ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Clinical Diagnosis & Chief Complaints -->
        @if ($prescription->diagnosis || $prescription->symptoms)
            <div class="mb-6 border-b border-dashed border-brand-100 pb-4">
                @if ($prescription->symptoms)
                    <div class="mb-2">
                        <span class="text-xs font-bold text-muted uppercase">Chief Complaints:</span>
                        <p class="text-sm text-ink">{{ $prescription->symptoms }}</p>
                    </div>
                @endif
                @if ($prescription->diagnosis)
                    <div>
                        <span class="text-xs font-bold text-muted uppercase">Clinical Diagnosis:</span>
                        <p class="text-sm font-bold text-brand-600">{{ $prescription->diagnosis }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Rx Symbol & Medicines Section -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl font-black text-brand-600 font-serif">℞</span>
                <h3 class="text-base font-bold text-ink">Prescribed Medicines</h3>
            </div>

            @if (is_array($prescription->medicines) && count($prescription->medicines) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-brand-100 text-muted font-bold">
                                <th class="py-2.5 px-2">#</th>
                                <th class="py-2.5 px-2">Medicine Name &amp; Strength</th>
                                <th class="py-2.5 px-2">Dosage / Frequency</th>
                                <th class="py-2.5 px-2">Duration</th>
                                <th class="py-2.5 px-2">Instructions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            @foreach ($prescription->medicines as $idx => $med)
                                <tr class="text-ink">
                                    <td class="py-3 px-2 font-bold">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-2 font-bold text-sm">{{ $med['name'] ?? $med['medicine'] ?? 'Medicine' }}</td>
                                    <td class="py-3 px-2 font-semibold text-brand-600">{{ $med['frequency'] ?? $med['dosage'] ?? '1+0+1' }}</td>
                                    <td class="py-3 px-2">{{ $med['duration'] ?? '7 days' }}</td>
                                    <td class="py-3 px-2 text-muted italic">{{ $med['instructions'] ?? $med['timing'] ?? 'After food' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-muted italic">Consultation advice given as documented below.</p>
            @endif
        </div>

        <!-- Recommended Diagnostic Tests -->
        @if (!empty($prescription->tests_recommended))
            <div class="bg-surface-alt rounded-2xl p-4 mb-6 border border-brand-100 text-xs">
                <span class="font-bold text-brand-600 block mb-1">Recommended Diagnostic Tests / Investigations:</span>
                @if (is_array($prescription->tests_recommended))
                    <ul class="list-disc list-inside text-ink space-y-0.5">
                        @foreach ($prescription->tests_recommended as $tst)
                            <li>{{ $tst }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-ink">{{ $prescription->tests_recommended }}</p>
                @endif
            </div>
        @endif

        <!-- Doctor's Advice & Follow Up -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-dashed border-brand-100 pt-4 text-xs mb-8">
            @if ($prescription->advice)
                <div>
                    <span class="font-bold text-muted block mb-1">Special Advice:</span>
                    <p class="text-ink leading-relaxed">{{ $prescription->advice }}</p>
                </div>
            @endif
            @if ($prescription->follow_up_date)
                <div>
                    <span class="font-bold text-muted block mb-1">Next Follow-up Date:</span>
                    <p class="font-bold text-brand-600">{{ $prescription->follow_up_date->format('M d, Y') }}</p>
                </div>
            @endif
        </div>

        <!-- Doctor Digital Signature Footer -->
        <div class="border-t border-brand-100 pt-6 flex justify-between items-end">
            <div class="text-[10px] text-muted max-w-xs">
                Generated via MediQueue Digital Healthcare System.<br>
                Valid without physical signature when verified with official QR code.
            </div>
            <div class="text-center">
                <div class="h-10 border-b border-ink/40 w-40 mx-auto flex items-end justify-center pb-1">
                    <span class="font-serif italic text-sm text-brand-600 font-bold">Dr. {{ substr($prescription->doctor->display_name ?? $prescription->doctor->user->name, 0, 15) }}</span>
                </div>
                <span class="text-[10px] text-muted block mt-1">Doctor's Digital Signature</span>
            </div>
        </div>

    </div>

</div>

<style>
@media print {
    .no-print, nav, header { display: none !important; }
    body { background: white !important; }
    .printable { box-shadow: none !important; border: 1px solid #ccc !important; padding: 20px !important; }
}
</style>
@endsection