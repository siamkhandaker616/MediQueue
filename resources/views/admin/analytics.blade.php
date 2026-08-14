<x-layouts.staff>
    <x-slot name="title">Analytics</x-slot>

    <div class="mb-6">
        <p class="text-sm text-muted">FR-20 · admin analytics</p>
        <h1 class="text-2xl font-bold tracking-tight">Hospital overview</h1>
        <p class="mt-1 text-sm text-muted">{{ now()->format('F Y') }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Total appointments</p>
            <p class="mt-1 text-3xl font-bold text-brand-600">{{ $appointments }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Total revenue</p>
            <p class="mt-1 text-3xl font-bold text-accent-600">৳{{ number_format($revenue) }}</p>
            <p class="text-xs text-muted">paid consultations</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Cancellation rate</p>
            <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $cancellationRate }}%</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Departments</p>
            <p class="mt-1 text-3xl font-bold">{{ $byDepartment->count() }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="font-semibold">Appointments by department</h2>
            <div class="mt-4 h-64"><canvas id="deptChart"></canvas></div>
        </div>

        <div class="card p-5">
            <h2 class="font-semibold">Appointment status</h2>
            <div class="mt-4 h-64"><canvas id="statusChart"></canvas></div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="font-semibold">Revenue — last 7 days</h2>
            <div class="mt-4 h-64"><canvas id="revenueChart"></canvas></div>
        </div>

        <div class="card p-5">
            <h2 class="font-semibold">Peak hours</h2>
            <div class="mt-4 h-64"><canvas id="hoursChart"></canvas></div>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-brand-100 px-5 py-4">
            <h2 class="font-semibold">Doctor performance</h2>
        </div>
        @if ($doctors->isEmpty())
            <p class="px-5 py-12 text-center text-sm text-muted">No doctor data yet.</p>
        @else
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
                                <td class="table-cell text-muted">{{ $doctor['department'] }}</td>
                                <td class="table-cell text-muted">{{ $doctor['appointments'] }}</td>
                                <td class="table-cell">
                                    @if ($doctor['rating'])
                                        <span class="inline-flex items-center gap-1 text-amber-500">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                                            <span class="text-ink">{{ $doctor['rating'] }}</span>
                                        </span>
                                    @else
                                        <span class="text-xs text-muted">—</span>
                                    @endif
                                </td>
                                <td class="table-cell font-medium text-accent-700">৳{{ number_format($doctor['revenue']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            const brand = getComputedStyle(document.documentElement).getPropertyValue('--brand-500').trim();
            const brand2 = getComputedStyle(document.documentElement).getPropertyValue('--brand-200').trim();
            const accent = getComputedStyle(document.documentElement).getPropertyValue('--accent-500').trim();

            @php
                $chartData = [
                    'departments' => $byDepartment,
                    'status' => $statusBreakdown,
                    'revenue' => $weeklyRevenue,
                    'hours' => $peakHours,
                ];
            @endphp

            const data = @json($chartData);

            const rgb = (v, a = 1) => `rgba(${v.split(' ').join(',')}, ${a})`;

            new Chart(document.getElementById('deptChart'), {
                type: 'bar',
                data: {
                    labels: data.departments.map(d => d.name),
                    datasets: [{
                        label: 'Appointments',
                        data: data.departments.map(d => d.count),
                        backgroundColor: rgb(brand, 0.8),
                        borderRadius: 6,
                    }],
                },
                options: { plugins: { legend: { display: false } }, maintainAspectRatio: false },
            });

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data.status),
                    datasets: [{
                        data: Object.values(data.status),
                        backgroundColor: [rgb(brand), rgb(accent), rgb(brand2), rgb('229 231 235'), rgb('252 211 77')],
                        borderWidth: 0,
                    }],
                },
                options: { plugins: { legend: { position: 'right' } }, maintainAspectRatio: false },
            });

            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: data.revenue.map(d => d.label),
                    datasets: [{
                        label: 'Revenue (৳)',
                        data: data.revenue.map(d => d.total),
                        borderColor: rgb(accent),
                        backgroundColor: rgb(accent, 0.15),
                        fill: true,
                        tension: 0.35,
                    }],
                },
                options: { maintainAspectRatio: false },
            });

            new Chart(document.getElementById('hoursChart'), {
                type: 'bar',
                data: {
                    labels: data.hours.map(h => h.label),
                    datasets: [{
                        label: 'Appointments',
                        data: data.hours.map(h => h.count),
                        backgroundColor: rgb(brand2, 0.9),
                        borderRadius: 6,
                    }],
                },
                options: { plugins: { legend: { display: false } }, maintainAspectRatio: false },
            });
        </script>
    @endpush
</x-layouts.staff>
