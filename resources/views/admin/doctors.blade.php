<x-layouts.staff>
    <x-slot name="title">Doctors</x-slot>

    @php
        $doctorsJson = $doctors
            ->map(fn ($doctor) => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'email' => $doctor->user->email,
                'department' => $doctor->department->name ?? null,
                'qualifications' => $doctor->qualifications,
                'specialties' => $doctor->specialties ? (is_array($doctor->specialties) ? implode(', ', $doctor->specialties) : $doctor->specialties) : null,
                'experience_years' => $doctor->experience_years,
                'consultation_fee' => (string) $doctor->consultation_fee,
                'languages' => $doctor->languages ? (is_array($doctor->languages) ? implode(', ', $doctor->languages) : $doctor->languages) : null,
                'bio' => $doctor->bio,
                'is_active' => (bool) $doctor->is_active,
                'leaves' => $doctor->leaves
                    ->sortByDesc('date')
                    ->map(fn ($leave) => [
                        'id' => $leave->id,
                        'date' => $leave->date->format('D, j M Y'),
                        'reason' => $leave->reason,
                        'status' => $leave->status,
                        'approve_url' => route('admin.leaves.approve', ['leave' => $leave->id]),
                        'reject_url' => route('admin.leaves.reject', ['leave' => $leave->id]),
                    ])
                    ->values(),
            ])
            ->values();
    @endphp

    <div x-data="doctorsList(@js($doctorsJson))">

        <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm text-muted">Admin · doctor management</p>
                <h1 class="text-2xl font-bold tracking-tight">Doctors</h1>
            </div>
            <a href="{{ route('admin.doctors.create') }}" class="btn-primary">+ Add doctor</a>
        </div>

        @if ($doctors->isEmpty())
            <div class="card px-5 py-16 text-center">
                <p class="text-sm text-muted">No doctors registered yet.</p>
            </div>
        @else
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px]">
                        <thead>
                            <tr>
                                <th class="table-head">Doctor</th>
                                <th class="table-head">Department</th>
                                <th class="table-head">Qualification</th>
                                <th class="table-head">Fee</th>
                                <th class="table-head">Appointments</th>
                                <th class="table-head">Rating</th>
                                <th class="table-head">Status</th>
                                <th class="table-head text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-50">
                            @foreach ($doctors as $doctor)
                                <tr>
                                    <td class="table-cell">
                                        <button type="button" @click="openDoctor({{ $doctor->id }})"
                                                class="text-left font-medium text-brand-700 transition hover:text-brand-600 hover:underline">
                                            {{ $doctor->name }}
                                        </button>
                                        <p class="text-xs text-muted">{{ $doctor->user->email }}</p>
                                    </td>
                                    <td class="table-cell text-muted">{{ $doctor->department->name ?? '—' }}</td>
                                    <td class="table-cell text-muted">{{ $doctor->qualifications }}</td>
                                    <td class="table-cell text-muted">৳{{ $doctor->consultation_fee }}</td>
                                    <td class="table-cell text-muted">{{ $doctor->appointments_count }}</td>
                                    <td class="table-cell">
                                        @if ($doctor->rating)
                                            <span class="text-amber-500">{{ number_format((float) $doctor->rating, 1) }}/5</span>
                                        @else
                                            <span class="text-xs text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        <span class="badge {{ $doctor->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200' : 'bg-brand-50 text-muted ring-1 ring-inset ring-brand-200' }}">
                                            {{ $doctor->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="table-cell text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn-outline !px-3 !py-1 !text-xs">Edit</a>
                                            <form method="POST" action="{{ route('admin.doctors.toggle', $doctor) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn-outline !px-3 !py-1 !text-xs">
                                                    {{ $doctor->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <template x-teleport="body">
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 x-transition.opacity @keydown.escape.window="close()"
                 @click.self="close()" style="background: rgba(15, 23, 42, 0.5);">
                <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-surface p-6 shadow-2xl" @click.stop>
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-xl font-bold tracking-tight text-ink" x-text="selected ? selected.name : ''"></h2>
                            <p class="text-sm text-muted" x-text="selected ? selected.email : ''"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span x-show="selected && selected.is_active"
                                  class="badge bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">Active</span>
                            <span x-show="selected && !selected.is_active"
                                  class="badge bg-brand-50 text-muted ring-1 ring-inset ring-brand-200">Inactive</span>
                            <button type="button" @click="close()" class="rounded-lg p-1.5 text-muted transition hover:bg-brand-50 hover:text-ink">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-x-6 gap-y-3 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Department</p>
                            <p class="mt-0.5 text-sm font-medium" x-text="selected ? (selected.department || '—') : ''"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Qualification</p>
                            <p class="mt-0.5 text-sm font-medium" x-text="selected ? (selected.qualifications || '—') : ''"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Specialties</p>
                            <p class="mt-0.5 text-sm font-medium" x-text="selected ? (selected.specialties || '—') : ''"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Languages</p>
                            <p class="mt-0.5 text-sm font-medium" x-text="selected ? (selected.languages || '—') : ''"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Experience</p>
                            <p class="mt-0.5 text-sm font-medium" x-text="selected ? (selected.experience_years + ' years') : ''"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Consultation fee</p>
                            <p class="mt-0.5 text-sm font-medium" x-text="selected ? ('৳' + Number(selected.consultation_fee).toLocaleString()) : ''"></p>
                        </div>
                        <div class="sm:col-span-2" x-show="selected && selected.bio">
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Bio</p>
                            <p class="mt-0.5 text-sm text-muted whitespace-pre-line" x-text="selected.bio"></p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold">Leave requests</h3>
                            <span class="text-xs text-muted" x-text="selected ? selected.leaves.length + ' total' : ''"></span>
                        </div>

                        <template x-if="selected && selected.leaves.length === 0">
                            <p class="mt-3 rounded-xl border border-dashed border-brand-200 px-4 py-6 text-center text-sm text-muted">No leave requests.</p>
                        </template>

                        <div class="mt-3 divide-y divide-brand-50 rounded-xl border border-brand-100">
                            <template x-for="leave in (selected ? selected.leaves : [])" :key="leave.id">
                                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium" x-text="leave.date"></p>
                                        <p class="text-xs text-muted" x-text="leave.reason"></p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span x-show="leave.status === 'approved'"
                                              class="badge bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">Approved</span>
                                        <span x-show="leave.status === 'rejected'"
                                              class="badge bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200">Rejected</span>
                                        <template x-if="leave.status === 'pending'">
                                            <span class="badge bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200">Pending</span>
                                        </template>
                                        <template x-if="leave.status === 'pending'">
                                            <div class="flex gap-2">
                                                <form method="POST" :action="leave.approve_url" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="btn-primary !px-3 !py-1 !text-xs">Approve</button>
                                                </form>
                                                <form method="POST" :action="leave.reject_url" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="btn-outline !px-3 !py-1 !text-xs">Reject</button>
                                                </form>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function doctorsList(initialData) {
            return {
                doctors: initialData,
                selected: null,
                open: false,
                openDoctor(id) {
                    this.selected = this.doctors.find((d) => d.id === id) || null;
                    this.open = true;
                },
                close() {
                    this.open = false;
                    this.selected = null;
                },
            };
        }
    </script>
</x-layouts.staff>