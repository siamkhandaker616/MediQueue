<x-layouts.staff>
    <x-slot name="title">Queue dashboard</x-slot>

    @php
        $badges = [
            'scheduled' => 'bg-brand-100 text-brand-700 ring-1 ring-inset ring-brand-200',
            'checked_in' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200',
            'in_progress' => 'bg-accent-100 text-accent-700 ring-1 ring-inset ring-accent-200',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200',
            'cancelled' => 'bg-gray-100 text-gray-500 ring-1 ring-inset ring-gray-200',
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">{{ now()->format('l, j F Y') }}</p>
            <h1 class="text-2xl font-bold tracking-tight">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ str($doctor->name)->after('Dr. ') }}</h1>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Total today</p>
            <p class="mt-1 text-3xl font-bold">{{ $appointments->count() }}</p>
            <p class="text-xs text-muted">appointments</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Waiting</p>
            <p class="mt-1 text-3xl font-bold text-amber-600">{{ $waiting }}</p>
            <p class="text-xs text-muted">in queue</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">In progress</p>
            <p class="mt-1 text-3xl font-bold text-accent-600">{{ $inProgress }}</p>
            <p class="text-xs text-muted">with doctor</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Served</p>
            <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $served }}</p>
            <p class="text-xs text-muted">completed</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <div class="flex items-center justify-between border-b border-brand-100 px-5 py-4">
                <h2 class="font-semibold">Today's queue</h2>
                <span class="badge bg-brand-100 text-brand-700">{{ $appointments->count() }} patients</span>
            </div>

            @if ($appointments->isEmpty())
                <p class="px-5 py-12 text-center text-sm text-muted">No appointments scheduled for today.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px]">
                        <thead>
                            <tr>
                                <th class="table-head">Token</th>
                                <th class="table-head">Patient</th>
                                <th class="table-head">Time</th>
                                <th class="table-head">Status</th>
                                <th class="table-head text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-50">
                            @foreach ($appointments as $appointment)
                                <tr class="{{ $appointment->status === 'in_progress' ? 'bg-brand-50/60' : '' }}">
                                    <td class="table-cell">
                                        <span class="font-mono text-xs font-semibold text-brand-700">{{ $appointment->token_number }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <a href="{{ route('doctor.patients.show', $appointment->patient) }}" class="flex items-center gap-2 group">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                                                {{ strtoupper(substr($appointment->patient->name, 0, 1)) }}
                                            </span>
                                            <span class="font-medium group-hover:text-brand-700">{{ $appointment->patient->name }}</span>
                                        </a>
                                    </td>
                                    <td class="table-cell text-muted">{{ $appointment->time_slot }}</td>
                                    <td class="table-cell">
                                        <span class="badge {{ $badges[$appointment->status] ?? $badges['scheduled'] }}">
                                            {{ ucwords(str_replace('_', ' ', $appointment->status)) }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex justify-end gap-2">
                                            @if ($appointment->status === 'scheduled')
                                                <form method="POST" action="{{ route('doctor.queue.status', $appointment) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="checked_in">
                                                    <button class="btn-outline !px-3 !py-1 !text-xs">Check in</button>
                                                </form>
                                            @elseif ($appointment->status === 'checked_in')
                                                <form method="POST" action="{{ route('doctor.queue.status', $appointment) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button class="btn-primary !px-3 !py-1 !text-xs">Start</button>
                                                </form>
                                            @elseif ($appointment->status === 'in_progress')
                                                <a href="{{ route('doctor.prescriptions.create', $appointment) }}" class="btn-outline !px-3 !py-1 !text-xs">Prescribe</a>
                                                <form method="POST" action="{{ route('doctor.queue.status', $appointment) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button class="btn-primary !px-3 !py-1 !text-xs">Done</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-muted">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="font-semibold">Now serving</h2>
                @if ($nowServing)
                    <div class="mt-4 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 p-5 text-white">
                        <p class="text-xs font-medium uppercase tracking-wider text-brand-100">Token</p>
                        <p class="mt-1 text-3xl font-bold">{{ $nowServing->token_number }}</p>
                        <p class="mt-2 text-lg font-semibold">{{ $nowServing->patient->name }}</p>
                        <p class="text-sm text-brand-100">{{ $nowServing->time_slot }}</p>
                        <div class="mt-4">
                            <a href="{{ route('doctor.prescriptions.create', $nowServing) }}" class="rounded-lg bg-white/20 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/30">
                                Create prescription
                            </a>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-muted">No patient is currently with you.</p>
                @endif
            </div>

            <div class="card p-5">
                <h2 class="font-semibold">Next up</h2>
                @if ($nextUp)
                    <div class="mt-3 rounded-xl border border-accent-200 bg-accent-100/60 p-4">
                        <p class="text-xs font-medium text-accent-700">{{ $nextUp->token_number }} · {{ $nextUp->time_slot }}</p>
                        <p class="mt-1 font-semibold">{{ $nextUp->patient->name }}</p>
                    </div>
                @else
                    <p class="mt-3 text-sm text-muted">Queue is clear.</p>
                @endif
                <p class="mt-4 text-xs text-muted">FR-18 · queue position alerts fire as patients approach their turn.</p>
            </div>
        </div>
    </div>
</x-layouts.staff>
