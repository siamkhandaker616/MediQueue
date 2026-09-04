<x-layouts.staff>
    <x-slot name="title">Schedule &amp; leave</x-slot>

    @php
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $byDay = $schedules->groupBy('day_of_week');
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-06 · weekly availability</p>
            <h1 class="text-2xl font-bold tracking-tight">My schedule</h1>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-brand-100 px-5 py-4">
                <h2 class="font-semibold">Weekly slots</h2>
            </div>

            @if ($schedules->isEmpty())
                <p class="px-5 py-12 text-center text-sm text-muted">No schedule configured yet.</p>
            @else
                <div class="grid gap-4 p-5 sm:grid-cols-2">
                    @foreach ($days as $index => $day)
                        <div class="rounded-xl border border-brand-100 p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold {{ $index === (int) now()->format('w') ? 'text-brand-700' : '' }}">{{ $day }}</p>
                                @if ($index === (int) now()->format('w'))
                                    <span class="badge bg-brand-100 text-brand-700">Today</span>
                                @endif
                            </div>
                            @php
                                $shifts = $byDay->get((string) $index, $byDay->get($index, collect()));
                            @endphp
                            @if ($shifts->isEmpty())
                                <p class="mt-2 text-xs text-muted">Off</p>
                            @else
                                <ul class="mt-2 space-y-1">
                                    @foreach ($shifts as $shift)
                                        <li class="flex items-center justify-between text-sm">
                                            <span>{{ $shift->start_time }} – {{ $shift->end_time }}</span>
                                            <span class="text-xs text-muted">{{ $shift->slot_duration }} min slots</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="font-semibold">Request leave</h2>
                <p class="mt-1 text-sm text-muted">Booked patients are notified when leave is approved.</p>
                <form method="POST" action="{{ route('doctor.leave.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="date" class="label">Date</label>
                        <input id="date" name="date" type="date" min="{{ now()->toDateString() }}"
                               value="{{ old('date') }}" class="input mt-1" required>
                        @error('date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="reason" class="label">Reason</label>
                        <textarea id="reason" name="reason" rows="2" class="input mt-1" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary w-full">Submit request</button>
                </form>
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-brand-100 px-5 py-4">
                    <h2 class="font-semibold">Upcoming leave</h2>
                </div>
                @if ($leaves->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-muted">No leave booked.</p>
                @else
                    <ul class="divide-y divide-brand-50">
                        @foreach ($leaves as $leave)
                            <li class="flex items-center justify-between px-5 py-3">
                                <div>
                                    <p class="text-sm font-medium">{{ $leave->date->format('D, j M Y') }}</p>
                                    <p class="text-xs text-muted">{{ $leave->reason }}</p>
                                </div>
                                <span class="badge bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200">Requested</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-layouts.staff>
