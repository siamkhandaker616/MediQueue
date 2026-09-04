@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-ink tracking-tight">Medication Tracker</h1>
            <p class="text-muted text-sm mt-1">Track your daily medication adherence for active prescriptions.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('patient.medications.history') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition">
                <i class="fa-solid fa-calendar-days mr-1"></i> History
            </a>
            <a href="{{ route('patient.prescriptions.index') }}" class="bg-surface border border-brand-200 text-ink hover:border-brand-500 px-4 py-2.5 rounded-xl text-sm font-medium transition">
                &larr; Prescriptions
            </a>
        </div>
    </div>

    @php
        $slotLabels = ['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening'];
        $slotIcons = ['morning' => 'fa-sun', 'afternoon' => 'fa-cloud-sun', 'evening' => 'fa-moon'];
    @endphp

    @if ($items->isEmpty())
        <div class="bg-surface border border-brand-100 rounded-3xl p-12 text-center my-6">
            <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                <i class="fa-solid fa-pills"></i>
            </div>
            <h3 class="text-lg font-bold text-ink">No active medications</h3>
            <p class="text-muted text-sm mt-1 mb-6">Medications from prescriptions issued in the last 30 days will appear here.</p>
            <a href="{{ route('patient.prescriptions.index') }}" class="bg-brand-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-brand-700 transition text-sm">
                View Prescriptions
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($items as $item)
                @php
                    $freqParts = array_map('intval', explode('+', $item->frequency ?? '1+0+1'));
                    $activeSlots = [];
                    if (($freqParts[0] ?? 0) > 0) $activeSlots[] = 'morning';
                    if (($freqParts[1] ?? 0) > 0) $activeSlots[] = 'afternoon';
                    if (($freqParts[2] ?? 0) > 0) $activeSlots[] = 'evening';
                    if (empty($activeSlots)) $activeSlots = ['morning'];
                @endphp

                <div class="bg-surface border border-brand-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-5 flex flex-col sm:flex-row justify-between items-start gap-3">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl shrink-0">
                                <i class="fa-solid fa-capsules"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-ink text-base">{{ $item->medication_name }}</h3>
                                <p class="text-xs text-muted mt-0.5">
                                    {{ $item->dosage }} &middot; {{ $item->frequency }} &middot; {{ $item->duration ?? 'As directed' }}
                                </p>
                                @if ($item->instructions)
                                    <p class="text-xs text-muted mt-1">{{ $item->instructions }}</p>
                                @endif
                                <p class="text-xs text-brand-600 font-medium mt-1">
                                    {{ $item->doctor_name }} &middot; {{ $item->department_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-brand-100 px-5 py-3 flex flex-wrap gap-3">
                        @foreach ($activeSlots as $slot)
                            @php
                                $logKey = $item->id . '_' . $slot;
                                $log = $todayLogs->get($logKey);
                                $currentStatus = $log->status ?? null;
                            @endphp
                            <div x-data="{ status: @js($currentStatus), loading: false }"
                                 class="flex items-center gap-2 text-sm">
                                <span class="text-muted font-medium text-xs">
                                    <i class="fa-solid {{ $slotIcons[$slot] }} mr-1"></i>{{ $slotLabels[$slot] }}
                                </span>

                                <button
                                    @click="
                                        loading = true;
                                        fetch('{{ route('patient.medications.log') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                prescription_item_id: {{ $item->id }},
                                                slot: '{{ $slot }}',
                                                status: 'taken',
                                                scheduled_date: '{{ $today->toDateString() }}'
                                            })
                                        }).then(r => r.json()).then(d => {
                                            status = d.status;
                                            loading = false;
                                        })
                                    "
                                    :class="status === 'taken' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-surface border-brand-200 text-muted hover:border-emerald-400 hover:text-emerald-600'"
                                    :disabled="loading"
                                    class="px-3 py-1.5 rounded-lg border text-xs font-bold transition">
                                    <i class="fa-solid fa-check mr-0.5"></i> Taken
                                </button>

                                <button
                                    @click="
                                        loading = true;
                                        fetch('{{ route('patient.medications.log') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                prescription_item_id: {{ $item->id }},
                                                slot: '{{ $slot }}',
                                                status: 'skipped',
                                                scheduled_date: '{{ $today->toDateString() }}'
                                            })
                                        }).then(r => r.json()).then(d => {
                                            status = d.status;
                                            loading = false;
                                        })
                                    "
                                    :class="status === 'skipped' ? 'bg-amber-500 text-white border-amber-500' : 'bg-surface border-brand-200 text-muted hover:border-amber-400 hover:text-amber-600'"
                                    :disabled="loading"
                                    class="px-3 py-1.5 rounded-lg border text-xs font-bold transition">
                                    <i class="fa-solid fa-forward mr-0.5"></i> Skip
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
