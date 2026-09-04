@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <!-- Header & Quick Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-ink tracking-tight">My Visit History</h1>
            <p class="text-muted text-sm mt-1">Review your past consultations, upcoming appointments, and medical tokens.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('patient.medical-profile.edit') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-heart-pulse text-rose-500"></i> Medical Profile
            </a>
            <a href="{{ route('patient.reports.index') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-file-medical text-brand-600"></i> Medical Reports
            </a>
            <a href="{{ route('appointments.create') }}" class="bg-brand-600 text-white hover:bg-brand-700 px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> New Booking
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex border-b border-brand-100 mb-6 gap-2 sm:gap-6 overflow-x-auto text-sm font-medium">
        <a href="{{ route('patient.history', ['tab' => 'all']) }}" class="pb-3 px-2 transition border-b-2 {{ $tab === 'all' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent text-muted hover:text-ink' }}">
            All Visits ({{ $stats['total'] }})
        </a>
        <a href="{{ route('patient.history', ['tab' => 'upcoming']) }}" class="pb-3 px-2 transition border-b-2 {{ $tab === 'upcoming' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent text-muted hover:text-ink' }}">
            Upcoming ({{ $stats['upcoming'] }})
        </a>
        <a href="{{ route('patient.history', ['tab' => 'completed']) }}" class="pb-3 px-2 transition border-b-2 {{ $tab === 'completed' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent text-muted hover:text-ink' }}">
            Completed ({{ $stats['completed'] }})
        </a>
        <a href="{{ route('patient.history', ['tab' => 'cancelled']) }}" class="pb-3 px-2 transition border-b-2 {{ $tab === 'cancelled' ? 'border-brand-600 text-brand-600 font-bold' : 'border-transparent text-muted hover:text-ink' }}">
            Cancelled ({{ $stats['cancelled'] }})
        </a>
    </div>

    <!-- Visit Cards List -->
    @if ($appointments->isEmpty())
        <div class="bg-surface border border-brand-100 rounded-3xl p-12 text-center my-6">
            <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-regular fa-calendar-xmark"></i>
            </div>
            <h3 class="text-lg font-bold text-ink">No appointments found</h3>
            <p class="text-muted text-sm mt-1 mb-6">You don't have any appointments under this category yet.</p>
            <a href="{{ route('appointments.create') }}" class="bg-brand-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-brand-700 transition text-sm">
                Book an Appointment
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($appointments as $apt)
                <div class="bg-surface border border-brand-100 rounded-2xl p-6 shadow-sm hover:border-brand-300 transition flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    
                    <!-- Doctor & Department Details -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-lg shrink-0">
                            {{ substr($apt->doctor->display_name ?? $apt->doctor->user->name ?? 'Dr', 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-ink text-base">Dr. {{ $apt->doctor->display_name ?? $apt->doctor->user->name }}</h3>
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $apt->status === 'completed' ? 'bg-emerald-500/10 text-emerald-600' : ($apt->status === 'cancelled' ? 'bg-rose-500/10 text-rose-600' : 'bg-brand-50 text-brand-600') }}">
                                    {{ ucfirst(str_replace('_', ' ', $apt->status)) }}
                                </span>
                            </div>
                            <p class="text-xs text-brand-600 font-medium">{{ $apt->department->name }} &bull; {{ $apt->department->locationLabel() }}</p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted mt-2">
                                <span><i class="fa-regular fa-calendar text-brand-600 mr-1"></i> {{ $apt->date->format('M d, Y') }}</span>
                                <span><i class="fa-regular fa-clock text-brand-600 mr-1"></i> {{ $apt->time_slot }}</span>
                                <span><i class="fa-solid fa-ticket text-brand-600 mr-1"></i> <strong class="text-ink">{{ $apt->token_number }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end border-t md:border-t-0 pt-3 md:pt-0 border-brand-100">
                        <!-- Token View -->
                        <a href="{{ route('appointments.show', $apt) }}" class="bg-surface-alt hover:bg-brand-50 border border-brand-100 text-ink hover:text-brand-700 px-4 py-2 rounded-xl text-xs font-semibold transition">
                            View Token
                        </a>

                        <!-- Reschedule Button (if upcoming) -->
                        @if ($apt->canBeRescheduled())
                            <a href="{{ route('appointments.reschedule', $apt) }}" class="bg-surface hover:bg-brand-50 border border-brand-200 text-ink px-4 py-2 rounded-xl text-xs font-semibold transition">
                                Reschedule
                            </a>
                        @endif

                        <!-- Receipt Button -->
                        @if ($apt->payment)
                            <a href="{{ route('payments.receipt', $apt->payment) }}" class="bg-brand-50 text-brand-700 px-4 py-2 rounded-xl text-xs font-semibold hover:bg-brand-100 transition">
                                Receipt
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $appointments->links() }}
        </div>
    @endif

</div>
@endsection