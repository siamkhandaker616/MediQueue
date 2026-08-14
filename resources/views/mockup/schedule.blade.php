<x-layouts.mockup>
    <x-slot name="title">Schedule &amp; leave</x-slot>

    @php
        $week = [
            ['day' => 'Mon', 'date' => '10', 'slots' => [['09:00', '12:00'], ['15:00', '17:00']]],
            ['day' => 'Tue', 'date' => '11', 'slots' => [['09:00', '13:00']]],
            ['day' => 'Wed', 'date' => '12', 'slots' => []],
            ['day' => 'Thu', 'date' => '13', 'slots' => [['09:00', '12:00'], ['15:00', '18:00']]],
            ['day' => 'Fri', 'date' => '14', 'slots' => [['09:00', '14:00']]],
            ['day' => 'Sat', 'date' => '15', 'slots' => [['10:00', '13:00']]],
            ['day' => 'Sun', 'date' => '16', 'slots' => []],
        ];

        $leaves = [
            ['doctor' => 'Dr. Sabrina Rahman', 'type' => 'Annual leave', 'from' => '20 Aug', 'to' => '24 Aug', 'status' => 'approved'],
            ['doctor' => 'Dr. Sabrina Rahman', 'type' => 'Personal day', 'from' => '2 Sep', 'to' => '2 Sep', 'status' => 'pending'],
            ['doctor' => 'Dr. Farhan Chowdhury', 'type' => 'Conference', 'from' => '5 Sep', 'to' => '6 Sep', 'status' => 'pending'],
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">Weekly availability</p>
            <h1 class="text-2xl font-bold tracking-tight">Schedule &amp; leave</h1>
        </div>
        <button class="btn-primary">Add slot</button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
        @foreach ($week as $day)
            <div class="card p-4 {{ count($day['slots']) === 0 ? 'opacity-70' : '' }}">
                <div class="flex items-center justify-between">
                    <p class="font-semibold">{{ $day['day'] }}</p>
                    <span class="text-xs text-muted">{{ $day['date'] }} Aug</span>
                </div>
                <div class="mt-3 space-y-2">
                    @forelse ($day['slots'] as $slot)
                        <div class="rounded-lg bg-brand-100 px-3 py-1.5 text-xs font-medium text-brand-700">
                            {{ $slot[0] }} – {{ $slot[1] }}
                        </div>
                    @empty
                        <p class="rounded-lg border border-dashed border-brand-200 px-3 py-2 text-center text-xs text-muted">Off</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mt-6 p-5">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold">Add availability slot</h2>
            <span class="badge bg-accent-100 text-accent-700">Form mockup</span>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="label">Day</label>
                <select class="input mt-1">
                    @foreach ($week as $day)
                        <option>{{ $day['day'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Start time</label>
                <input type="time" value="09:00" class="input mt-1">
            </div>
            <div>
                <label class="label">End time</label>
                <input type="time" value="12:00" class="input mt-1">
            </div>
            <div>
                <label class="label">Slot duration</label>
                <select class="input mt-1">
                    <option>15 min</option>
                    <option selected>20 min</option>
                    <option>30 min</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button class="btn-outline">Cancel</button>
            <button class="btn-primary">Save slot</button>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-brand-100 px-5 py-4">
            <h2 class="font-semibold">Leave requests</h2>
            <span class="badge bg-amber-50 text-amber-700">2 pending</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr>
                        <th class="table-head">Doctor</th>
                        <th class="table-head">Type</th>
                        <th class="table-head">From</th>
                        <th class="table-head">To</th>
                        <th class="table-head">Status</th>
                        <th class="table-head text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-50">
                    @foreach ($leaves as $leave)
                        <tr>
                            <td class="table-cell font-medium">{{ $leave['doctor'] }}</td>
                            <td class="table-cell text-muted">{{ $leave['type'] }}</td>
                            <td class="table-cell text-muted">{{ $leave['from'] }}</td>
                            <td class="table-cell text-muted">{{ $leave['to'] }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $leave['status'] === 'approved' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200' }}">
                                    {{ ucfirst($leave['status']) }}
                                </span>
                            </td>
                            <td class="table-cell text-right">
                                @if ($leave['status'] === 'pending')
                                    <button class="btn-primary !px-3 !py-1 !text-xs">Approve</button>
                                    <button class="btn-outline ml-1 !px-3 !py-1 !text-xs">Reject</button>
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
</x-layouts.mockup>
