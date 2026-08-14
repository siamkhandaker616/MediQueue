<x-layouts.staff>
    <x-slot name="title">Admin dashboard</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Hospital overview</h1>
        <p class="mt-1 text-sm text-muted">{{ now()->format('l, j F Y') }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Appointments today</p>
            <p class="mt-1 text-3xl font-bold text-brand-600">{{ $todayAppointments }}</p>
            <p class="text-xs text-muted">scheduled for {{ now()->format('D') }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Revenue today</p>
            <p class="mt-1 text-3xl font-bold text-accent-600">৳{{ number_format($todayRevenue) }}</p>
            <p class="text-xs text-muted">paid today</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Patients</p>
            <p class="mt-1 text-3xl font-bold">{{ $totalPatients }}</p>
            <p class="text-xs text-muted">registered</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Doctors</p>
            <p class="mt-1 text-3xl font-bold">{{ $totalDoctors }}</p>
            <p class="text-xs text-muted">on staff</p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">All-time appointments</p>
            <p class="mt-1 text-3xl font-bold">{{ $totalAppointments }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">All-time revenue</p>
            <p class="mt-1 text-3xl font-bold text-accent-700">৳{{ number_format($totalRevenue) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Pending reviews</p>
            <div class="mt-1 flex items-center justify-between">
                <p class="text-3xl font-bold text-amber-600">{{ $pendingReviews }}</p>
                <a href="{{ route('admin.reviews') }}" class="btn-outline !px-3 !py-1.5 !text-xs">Moderate</a>
            </div>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-brand-100 px-5 py-4">
            <h2 class="font-semibold">Recent appointments</h2>
            <a href="{{ route('admin.analytics') }}" class="text-sm font-medium text-brand-700 hover:text-brand-600">Analytics →</a>
        </div>
        @if ($recentAppointments->isEmpty())
            <p class="px-5 py-12 text-center text-sm text-muted">No appointments yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr>
                            <th class="table-head">Date</th>
                            <th class="table-head">Patient</th>
                            <th class="table-head">Doctor</th>
                            <th class="table-head">Department</th>
                            <th class="table-head">Time</th>
                            <th class="table-head">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @foreach ($recentAppointments as $appointment)
                            <tr>
                                <td class="table-cell text-muted">{{ $appointment->date->format('d M Y') }}</td>
                                <td class="table-cell font-medium">{{ $appointment->patient->name }}</td>
                                <td class="table-cell text-muted">{{ $appointment->doctor->name }}</td>
                                <td class="table-cell text-muted">{{ $appointment->department->name ?? '—' }}</td>
                                <td class="table-cell text-muted">{{ $appointment->time_slot }}</td>
                                <td class="table-cell">
                                    <span class="badge bg-brand-100 text-brand-700">{{ ucwords(str_replace('_', ' ', $appointment->status)) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.staff>
