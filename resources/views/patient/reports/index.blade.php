@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8" x-data="{ uploadModal: false }">

    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-center font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-ink tracking-tight">Medical Reports &amp; Scans</h1>
            <p class="text-muted text-sm mt-1">Upload lab test results, radiology X-Rays, and diagnostic reports for doctor review.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('patient.history') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition">
                &larr; Visit History
            </a>
            <button @click="uploadModal = true" class="bg-brand-600 text-white hover:bg-brand-700 px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up"></i> Upload Report
            </button>
        </div>
    </div>

    <!-- Reports Grid -->
    @if ($reports->isEmpty())
        <div class="bg-surface border border-brand-100 rounded-3xl p-12 text-center my-6">
            <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-file-medical"></i>
            </div>
            <h3 class="text-lg font-bold text-ink">No medical reports uploaded yet</h3>
            <p class="text-muted text-sm mt-1 mb-6">Keep all your lab tests and diagnostic scans organized in one secure place.</p>
            <button @click="uploadModal = true" class="bg-brand-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-brand-700 transition text-sm">
                Upload Your First Report
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($reports as $report)
                <div class="bg-surface border border-brand-100 rounded-2xl p-5 shadow-sm hover:border-brand-300 transition flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-xs uppercase tracking-wider font-bold px-2.5 py-0.5 rounded-full bg-brand-50 text-brand-600">
                                {{ strtoupper($report->report_type) }}
                            </span>
                            <span class="text-xs text-muted">{{ $report->report_date ? $report->report_date->format('M d, Y') : $report->created_at->format('M d, Y') }}</span>
                        </div>
                        <h3 class="font-bold text-ink text-base line-clamp-1">{{ $report->file_name }}</h3>
                        <p class="text-xs text-muted mt-1 flex items-center gap-2">
                            <i class="fa-solid fa-paperclip text-brand-600"></i> {{ $report->formatted_size }} &bull; {{ $report->file_type }}
                        </p>
                        @if ($report->appointment)
                            <p class="text-xs text-brand-600 font-medium mt-2">
                                Linked to Dr. {{ $report->appointment->doctor->display_name ?? $report->appointment->doctor->user->name }} ({{ $report->appointment->date->format('M d, Y') }})
                            </p>
                        @endif
                    </div>

                    <div class="border-t border-brand-100 pt-4 mt-4 flex justify-between items-center text-xs">
                        <a href="{{ $report->download_url }}" target="_blank" class="bg-brand-50 text-brand-700 hover:bg-brand-100 font-bold px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-down"></i> View / Download
                        </a>
                        <form method="POST" action="{{ route('patient.reports.destroy', $report) }}" onsubmit="return confirm('Delete this report?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 font-medium">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $reports->links() }}
        </div>
    @endif

    <!-- Upload Modal -->
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="uploadModal = false" class="bg-surface border border-brand-100 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-ink">Upload Medical Report</h2>
                <button @click="uploadModal = false" class="text-muted hover:text-ink text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('patient.reports.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-4 text-sm">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-ink mb-1">Report Category</label>
                            <select name="report_type" class="w-full rounded-xl border-brand-200 bg-surface text-ink p-3">
                                <option value="lab">Lab Test (Blood/Urine)</option>
                                <option value="radiology">Radiology / X-Ray / CT</option>
                                <option value="prescription">Prescription</option>
                                <option value="cardiac">ECG / Echo</option>
                                <option value="other">Other Diagnostic</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-ink mb-1">Report Date</label>
                            <input type="date" name="report_date" value="{{ now()->toDateString() }}" class="w-full rounded-xl border-brand-200 bg-surface text-ink p-3">
                        </div>
                    </div>

                    @if ($appointments->isNotEmpty())
                        <div>
                            <label class="block font-semibold text-ink mb-1">Link to Appointment (Optional)</label>
                            <select name="appointment_id" class="w-full rounded-xl border-brand-200 bg-surface text-ink p-3">
                                <option value="">General Patient Record (No specific visit)</option>
                                @foreach ($appointments as $apt)
                                    <option value="{{ $apt->id }}">{{ $apt->date->format('M d, Y') }} - Dr. {{ $apt->doctor->display_name ?? $apt->doctor->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block font-semibold text-ink mb-1">Choose File (PDF, JPG, PNG - Max 10MB)</label>
                        <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-muted file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="uploadModal = false" class="px-4 py-2 rounded-xl border border-brand-200 text-muted hover:text-ink">Cancel</button>
                    <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-brand-700 shadow-sm">Upload Report</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection