@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-ink tracking-tight">Adherence History</h1>
            <p class="text-muted text-sm mt-1">Your medication adherence for the last 30 days.</p>
        </div>
        <a href="{{ route('patient.medications.index') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition">
            &larr; Tracker
        </a>
    </div>

    <div class="bg-surface border border-brand-100 rounded-2xl p-6 mb-8 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl {{ $overallPct >= 80 ? 'bg-emerald-50 text-emerald-600' : ($overallPct >= 50 ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-600') }} flex items-center justify-center text-xl font-black">
                {{ $overallPct }}%
            </div>
            <div>
                <p class="text-sm font-bold text-ink">Overall Adherence</p>
                <p class="text-xs text-muted">{{ $start->format('M d') }} &ndash; {{ $end->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-7 gap-2">
        @foreach (['S','M','T','W','T','F','S'] as $dayLabel)
            <div class="text-center text-xs font-bold text-muted pb-1">{{ $dayLabel }}</div>
        @endforeach

        {{-- Output empty cells before the start day of week for correct calendar alignment --}}
        @for ($i = 0; $i < $start->dayOfWeek; $i++)
            <div></div>
        @endfor

        @foreach ($days as $day)
            @php
                $pct = $day['pct'];
                if ($day['total'] == 0) {
                    $bgClass = 'bg-surface border-brand-100';
                    $textClass = 'text-muted';
                } elseif ($pct >= 80) {
                    $bgClass = 'bg-emerald-50 border-emerald-200';
                    $textClass = 'text-emerald-700';
                } elseif ($pct >= 50) {
                    $bgClass = 'bg-amber-50 border-amber-200';
                    $textClass = 'text-amber-700';
                } else {
                    $bgClass = 'bg-red-50 border-red-200';
                    $textClass = 'text-red-700';
                }
                $isToday = $day['date']->isToday();
            @endphp
            <div class="border rounded-xl p-2 text-center {{ $bgClass }} {{ $isToday ? 'ring-2 ring-brand-500' : '' }}">
                <p class="text-xs font-bold {{ $textClass }}">{{ $day['date']->format('d') }}</p>
                @if ($day['total'] > 0)
                    <p class="text-[10px] text-muted mt-0.5">{{ $day['taken'] }}/{{ $day['total'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="flex items-center gap-4 mt-6 text-xs text-muted">
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded bg-emerald-100 border border-emerald-200"></div> &ge;80%
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded bg-amber-100 border border-amber-200"></div> &ge;50%
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded bg-red-100 border border-red-200"></div> &lt;50%
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded bg-surface border border-brand-200"></div> No data
        </div>
    </div>

</div>
@endsection
