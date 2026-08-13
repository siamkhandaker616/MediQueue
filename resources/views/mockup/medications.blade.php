<x-layouts.mockup>
    <x-slot name="title">Medication tracker</x-slot>

    @php
        $medications = [
            [
                'name' => 'Paracetamol', 'dosage' => '500 mg', 'schedule' => '3x daily',
                'started' => '8 Aug', 'remaining' => 5, 'total' => 5, 'note' => 'After meals', 'active' => true,
            ],
            [
                'name' => 'Azithromycin', 'dosage' => '250 mg', 'schedule' => '1x daily',
                'started' => '8 Aug', 'remaining' => 3, 'total' => 3, 'note' => 'With food', 'active' => true,
            ],
            [
                'name' => 'Metformin', 'dosage' => '500 mg', 'schedule' => '2x daily',
                'started' => '28 Jul', 'remaining' => 11, 'total' => 30, 'note' => 'Refill soon', 'active' => true,
            ],
            [
                'name' => 'Cetirizine', 'dosage' => '10 mg', 'schedule' => '1x nightly',
                'started' => '15 Jul', 'remaining' => 0, 'total' => 10, 'note' => 'Course completed', 'active' => false,
            ],
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-16 | Medication tracker</p>
            <h1 class="text-2xl font-bold tracking-tight">Active medications</h1>
        </div>
        <span class="badge bg-accent-100 text-accent-700">2 active courses</span>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @foreach ($medications as $med)
            <div class="card p-5 {{ $med['active'] ? '' : 'opacity-60' }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">{{ $med['name'] }}</h2>
                        <p class="text-sm text-muted">{{ $med['dosage'] }} · {{ $med['schedule'] }}</p>
                    </div>
                    @if ($med['remaining'] === 0)
                        <span class="badge bg-brand-100 text-brand-700">Completed</span>
                    @elseif ($med['remaining'] <= 3)
                        <span class="badge bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200">Ending soon</span>
                    @else
                        <span class="badge bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">Active</span>
                    @endif
                </div>

                <div class="mt-4">
                    <div class="flex justify-between text-xs text-muted">
                        <span>Started {{ $med['started'] }}</span>
                        <span>{{ $med['remaining'] }} of {{ $med['total'] }} days left</span>
                    </div>
                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-brand-100">
                        @php $pct = $med['total'] > 0 ? round(($med['remaining'] / $med['total']) * 100) : 0; @endphp
                        <div class="h-full rounded-full {{ $med['remaining'] <= 3 ? 'bg-amber-400' : 'bg-accent-500' }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <p class="text-xs text-muted">{{ $med['note'] }}</p>
                    <div class="flex gap-2">
                        <button class="btn-outline !px-3 !py-1 !text-xs">Reminder</button>
                        @if ($med['active'] && $med['remaining'] > 0)
                            <button class="btn-outline !px-3 !py-1 !text-xs !text-brand-600 !border-brand-200">Refill</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mt-6 p-5">
        <h2 class="font-semibold">Refill reminders</h2>
        <p class="mt-1 text-sm text-muted">
            A reminder is triggered when fewer than 3 days of medication remain (FR-16). Metformin is due for a refill this week.
        </p>
    </div>
</x-layouts.mockup>
