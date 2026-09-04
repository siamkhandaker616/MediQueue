@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-ink tracking-tight">My Digital Prescriptions</h1>
            <p class="text-muted text-sm mt-1">Access, download, and print official medical prescriptions issued by your doctors.</p>
        </div>
        <a href="{{ route('patient.history') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition">
            &larr; Visit History
        </a>
        <a href="{{ route('patient.medications.index') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2">
            <i class="fa-solid fa-pills"></i> Medication Tracker
        </a>
    </div>

    @if ($prescriptions->isEmpty())
        <div class="bg-surface border border-brand-100 rounded-3xl p-12 text-center my-6">
            <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                ℞
            </div>
            <h3 class="text-lg font-bold text-ink">No prescriptions issued yet</h3>
            <p class="text-muted text-sm mt-1 mb-6">Prescriptions written by your doctors after consultation will appear here automatically.</p>
            <a href="{{ route('patient.history') }}" class="bg-brand-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-brand-700 transition text-sm">
                View Past Consultations
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($prescriptions as $rx)
                <div class="bg-surface border border-brand-100 rounded-2xl p-6 shadow-sm hover:border-brand-300 transition flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xl shrink-0">
                            ℞
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-ink text-base">Dr. {{ $rx->doctor->display_name ?? $rx->doctor->user->name }}</h3>
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-brand-50 text-brand-600 font-mono">
                                    {{ $rx->prescription_number }}
                                </span>
                            </div>
                            <p class="text-xs text-brand-600 font-medium">{{ $rx->doctor->department->name ?? 'General Medicine' }}</p>
                            <p class="text-xs text-ink mt-1 font-semibold">Diagnosis: {{ $rx->diagnosis ?? 'General Consultation' }}</p>
                            <div class="flex items-center gap-4 text-xs text-muted mt-2">
                                <span><i class="fa-regular fa-calendar text-brand-600 mr-1"></i> {{ $rx->created_at->format('M d, Y') }}</span>
                                @if (is_array($rx->medicines))
                                    <span><i class="fa-solid fa-pills text-brand-600 mr-1"></i> {{ count($rx->medicines) }} Medicines prescribed</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('patient.prescriptions.show', $rx) }}" class="bg-brand-600 text-white hover:bg-brand-700 px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-file-pdf"></i> View &amp; Print Prescription
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $prescriptions->links() }}
        </div>
    @endif

</div>
@endsection