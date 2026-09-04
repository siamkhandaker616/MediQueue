@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-ink tracking-tight">Medication Schedule & Tracker</h1>
            <p class="text-muted text-sm mt-1">Track your active prescriptions, daily doses, and refill countdowns.</p>
        </div>
        <a href="{{ route('patient.prescriptions.index') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition">
            &larr; All Prescriptions
        </a>
    </div>

    <!-- Monthly Intake Calendar Grid -->
    <div class="bg-surface border border-brand-100 rounded-3xl p-6 sm:p-8 mb-8 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-ink">{{ now()->format('F Y') }} Intake Calendar</h2>
            <span class="text-xs font-semibold px-3 py-1 bg-brand-50 text-brand-700 rounded-full">Daily Tracking</span>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold text-muted mb-2">
            <div>SUN</div>
            <div>MON</div>
            <div>TUE</div>
            <div>WED</div>
            <div>THU</div>
            <div>FRI</div>
            <div>SAT</div>
        </div>

        @php
            $startOfMonth = now()->startOfMonth();
            $daysInMonth = now()->daysInMonth;
            $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)
            $today = now()->day;
        @endphp

        <div class="grid grid-cols-7 gap-2 text-center text-xs">
            {{-- Render empty offset cells using Blade loop rather than echo in PHP --}}
            @for ($i = 0; $i < $startDayOfWeek; $i++)
                <div class="h-10 rounded-xl bg-surface-alt/40 border border-transparent"></div>
            @endfor

            @for ($day = 1; $day <= $daysInMonth; $day++)
                <div class="h-10 rounded-xl border flex items-center justify-center font-bold transition {{ $day === $today ? 'bg-brand-600 text-white border-brand-600 shadow-sm' : ($day < $today ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-700' : 'bg-surface-alt border-brand-100 text-ink') }}">
                    {{ $day }}
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection
