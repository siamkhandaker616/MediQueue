<x-layouts.mockup>
    <x-slot name="title">Queue dashboard</x-slot>

    @php
        $stats = [
            ['label' => 'Total today', 'value' => '24', 'hint' => 'appointments', 'tone' => 'text-ink'],
            ['label' => 'Waiting', 'value' => '7', 'hint' => 'in queue', 'tone' => 'text-amber-600'],
            ['label' => 'In progress', 'value' => '2', 'hint' => 'with doctor', 'tone' => 'text-accent-600'],
            ['label' => 'Served', 'value' => '15', 'hint' => 'completed', 'tone' => 'text-emerald-600'],
        ];

        $queue = [
            ['token' => 'CRD-0047', 'name' => 'Ayesha Rahman', 'time' => '09:00', 'reason' => 'Fever & fatigue', 'status' => 'served'],
            ['token' => 'CRD-0048', 'name' => 'Tanvir Ahmed', 'time' => '09:15', 'reason' => 'Blood pressure review', 'status' => 'served'],
            ['token' => 'CRD-0049', 'name' => 'Nusrat Jahan', 'time' => '09:30', 'reason' => 'Migraine', 'status' => 'served'],
            ['token' => 'CRD-0050', 'name' => 'Karim Uddin', 'time' => '09:45', 'reason' => 'Diabetes follow-up', 'status' => 'served'],
            ['token' => 'CRD-0051', 'name' => 'Farhana Islam', 'time' => '10:00', 'reason' => 'Skin rash', 'status' => 'served'],
            ['token' => 'CRD-0052', 'name' => 'Rafiul Hasan', 'time' => '10:15', 'reason' => 'Chest discomfort', 'status' => 'in-progress'],
            ['token' => 'CRD-0053', 'name' => 'Mehnaz Akter', 'time' => '10:30', 'reason' => 'Thyroid check', 'status' => 'waiting'],
            ['token' => 'CRD-0054', 'name' => 'Sohel Rana', 'time' => '10:45', 'reason' => 'Back pain', 'status' => 'waiting'],
            ['token' => 'CRD-0055', 'name' => 'Priya Das', 'time' => '11:00', 'reason' => 'Allergy consult', 'status' => 'waiting'],
        ];

        $badges = [
            'waiting' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200',
            'in-progress' => 'bg-accent-100 text-accent-700 ring-1 ring-inset ring-accent-200',
            'served' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200',
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">Saturday, 8 August</p>
            <h1 class="text-2xl font-bold tracking-tight">Good morning, Dr. Rahman</h1>
        </div>
        <div class="flex gap-2">
            <button class="btn-outline">Refresh</button>
            <button class="btn-primary">Next patient</button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="stat-card">
                <p class="text-sm font-medium text-muted">{{ $stat['label'] }}</p>
                <p class="mt-1 text-3xl font-bold {{ $stat['tone'] }}">{{ $stat['value'] }}</p>
                <p class="text-xs text-muted">{{ $stat['hint'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <div class="flex items-center justify-between border-b border-brand-100 px-5 py-4">
                <h2 class="font-semibold">Today's queue</h2>
                <span class="badge bg-brand-100 text-brand-700">{{ count($queue) }} patients</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr>
                            <th class="table-head">Token</th>
                            <th class="table-head">Patient</th>
                            <th class="table-head">Time</th>
                            <th class="table-head">Reason</th>
                            <th class="table-head">Status</th>
                            <th class="table-head text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @foreach ($queue as $index => $patient)
                            <tr class="{{ $patient['status'] === 'in-progress' ? 'bg-brand-50/60' : '' }}">
                                <td class="table-cell">
                                    <span class="font-mono text-xs font-semibold text-brand-700">{{ $patient['token'] }}</span>
                                </td>
                                <td class="table-cell">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                                            {{ strtoupper(substr($patient['name'], 0, 2)) }}
                                        </span>
                                        <span class="font-medium">{{ $patient['name'] }}</span>
                                    </div>
                                </td>
                                <td class="table-cell text-muted">{{ $patient['time'] }}</td>
                                <td class="table-cell text-muted">{{ $patient['reason'] }}</td>
                                <td class="table-cell">
                                    <span class="badge {{ $badges[$patient['status']] }}">
                                        {{ ucwords(str_replace('-', ' ', $patient['status'])) }}
                                    </span>
                                </td>
                                <td class="table-cell text-right">
                                    @if ($patient['status'] === 'waiting')
                                        <button class="btn-outline !px-3 !py-1 !text-xs">Start</button>
                                    @elseif ($patient['status'] === 'in-progress')
                                        <button class="btn-primary !px-3 !py-1 !text-xs">Done</button>
                                    @else
                                        <span class="text-xs text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="font-semibold">Now serving</h2>
                <div class="mt-4 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 p-5 text-white">
                    <p class="text-xs font-medium uppercase tracking-wider text-brand-100">Token</p>
                    <p class="mt-1 text-3xl font-bold">CRD-0052</p>
                    <p class="mt-2 text-lg font-semibold">Rafiul Hasan</p>
                    <p class="text-sm text-brand-100">Chest discomfort · 10:15</p>
                    <div class="mt-4 flex gap-2">
                        <button class="rounded-lg bg-white/20 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/30">Done</button>
                        <button class="rounded-lg border border-white/40 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/10">Hold</button>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="font-semibold">Next up</h2>
                <p class="mt-2 text-sm text-muted">Mehnaz Akter is 1 patient away.</p>
                <div class="mt-3 rounded-xl border border-accent-200 bg-accent-100/60 p-4">
                    <p class="text-xs font-medium text-accent-700">CRD-0053 · 10:30</p>
                    <p class="mt-1 font-semibold">Mehnaz Akter</p>
                    <p class="text-sm text-muted">Thyroid check</p>
                </div>
                <p class="mt-4 text-xs text-muted">Alert fires when the patient is 2 ahead (FR-18).</p>
            </div>
        </div>
    </div>
</x-layouts.mockup>
