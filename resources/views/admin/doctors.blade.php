<x-layouts.staff>
    <x-slot name="title">Doctors</x-slot>

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
                                    <p class="font-medium">{{ $doctor->name }}</p>
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
                                    <span class="badge {{ $doctor->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200' : 'bg-gray-100 text-gray-500 ring-1 ring-inset ring-gray-200' }}">
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
</x-layouts.staff>
