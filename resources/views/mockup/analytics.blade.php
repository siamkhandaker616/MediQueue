<x-layouts.mockup>
    <x-slot name="title">Analytics</x-slot>

    @php
        $stats = [
            ['label' => 'Appointments today', 'value' => '128', 'hint' => '+12% vs last week', 'tone' => 'text-brand-600'],
            ['label' => 'Revenue today', 'value' => '46,500', 'hint' => 'BDT · +8% vs last week', 'tone' => 'text-accent-600'],
            ['label' => 'New patients', 'value' => '18', 'hint' => 'this week', 'tone' => 'text-ink'],
            ['label' => 'Cancellation rate', 'value' => '4.2%', 'hint' => '-0.6% vs last month', 'tone' => 'text-emerald-600'],
        ];

        $departments = [
            ['name' => 'General Medicine', 'count' => 44, 'max' => 48],
            ['name' => 'Cardiology', 'count' => 32, 'max' => 48],
            ['name' => 'Pediatrics', 'count' => 28, 'max' => 48],
            ['name' => 'Dermatology', 'count' => 21, 'max' => 48],
            ['name' => 'ENT', 'count' => 15, 'max' => 48],
        ];

        $hours = [
            ['time' => '9am', 'value' => 12],
            ['time' => '10am', 'value' => 20],
            ['time' => '11am', 'value' => 26],
            ['time' => '12pm', 'value' => 18],
            ['time' => '1pm', 'value' => 10],
            ['time' => '2pm', 'value' => 16],
            ['time' => '3pm', 'value' => 22],
            ['time' => '4pm', 'value' => 19],
        ];

        $doctors = [
            ['name' => 'Dr. Sabrina Rahman', 'dept' => 'Cardiology', 'appointments' => 118, 'rating' => 4.8, 'revenue' => '59,000'],
            ['name' => 'Dr. Farhan Chowdhury', 'dept' => 'General Medicine', 'appointments' => 96, 'rating' => 4.5, 'revenue' => '38,400'],
            ['name' => 'Dr. Tanvir Hasan', 'dept' => 'Dermatology', 'appointments' => 82, 'rating' => 4.2, 'revenue' => '32,800'],
            ['name' => 'Dr. Nabila Khan', 'dept' => 'Pediatrics', 'appointments' => 74, 'rating' => 4.7, 'revenue' => '29,600'],
        ];
    @endphp

    <div class="mb-6">
        <p class="text-sm text-muted">FR-20 | Admin analytics</p>
        <h1 class="text-2xl font-bold tracking-tight">Hospital overview</h1>
        <p class="mt-1 text-sm text-muted">August 2026 · week of 3–9 Aug</p>
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

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="font-semibold">Appointments by department</h2>
            <div class="mt-4 space-y-4">
                @foreach ($departments as $dept)
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium">{{ $dept['name'] }}</span>
                            <span class="text-muted">{{ $dept['count'] }}</span>
                        </div>
                        <div class="mt-1 h-2.5 overflow-hidden rounded-full bg-brand-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600"
                                 style="width: {{ round(($dept['count'] / $dept['max']) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Appointment status</h2>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-brand-500"></span><span class="text-xs text-muted">Completed</span>
                    <span class="ml-1 h-2 w-2 rounded-full bg-accent-500"></span><span class="text-xs text-muted">In progress</span>
                    <span class="ml-1 h-2 w-2 rounded-full bg-amber-300"></span><span class="text-xs text-muted">Waiting</span>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-center">
                <div class="relative h-44 w-44 rounded-full"
                     style="background: conic-gradient(var(--brand-500) 0 68%, var(--accent-500) 68% 86%, var(--brand-200) 86% 100%)">
                    <div class="absolute inset-4 flex items-center justify-center rounded-full bg-surface">
                        <div class="text-center">
                            <p class="text-2xl font-bold">128</p>
                            <p class="text-xs text-muted">appointments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="font-semibold">Peak hours</h2>
            <div class="mt-4 flex h-40 items-end gap-3">
                @foreach ($hours as $hour)
                    <div class="flex flex-1 flex-col items-center gap-1">
                        <div class="w-full rounded-t-lg {{ $loop->index >= 2 && $loop->index <= 3 ? 'bg-brand-500' : 'bg-brand-200' }}"
                             style="height: {{ $hour['value'] * 3 }}px"></div>
                        <span class="text-[10px] text-muted">{{ $hour['time'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-5">
            <h2 class="font-semibold">Revenue trend</h2>
            <div class="mt-4 flex h-40 items-end gap-3">
                @for ($i = 0; $i < 7; $i++)
                    @php
                        $heights = [34, 40, 30, 46, 52, 44, 58];
                        $bar = $heights[$i];
                    @endphp
                    <div class="flex flex-1 flex-col items-center gap-1">
                        <div class="w-full rounded-t-lg bg-gradient-to-t from-accent-400 to-accent-500" style="height: {{ $bar * 2.5 }}px"></div>
                        <span class="text-[10px] text-muted">{{ ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][$i] }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-brand-100 px-5 py-4">
            <h2 class="font-semibold">Doctor performance</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr>
                        <th class="table-head">Doctor</th>
                        <th class="table-head">Department</th>
                        <th class="table-head">Appointments</th>
                        <th class="table-head">Avg rating</th>
                        <th class="table-head">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-50">
                    @foreach ($doctors as $doctor)
                        <tr>
                            <td class="table-cell font-medium">{{ $doctor['name'] }}</td>
                            <td class="table-cell text-muted">{{ $doctor['dept'] }}</td>
                            <td class="table-cell text-muted">{{ $doctor['appointments'] }}</td>
                            <td class="table-cell">
                                <span class="inline-flex items-center gap-1 text-amber-500">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                                    <span class="text-ink">{{ $doctor['rating'] }}</span>
                                </span>
                            </td>
                            <td class="table-cell font-medium text-accent-700">৳{{ $doctor['revenue'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.mockup>
