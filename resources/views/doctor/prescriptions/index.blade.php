<x-layouts.staff>
    <x-slot name="title">Prescriptions</x-slot>

    <div class="mb-6">
        <p class="text-sm text-muted">FR-15 · issued prescriptions</p>
        <h1 class="text-2xl font-bold tracking-tight">Prescription history</h1>
    </div>

    @if ($prescriptions->isEmpty())
        <div class="card px-5 py-16 text-center">
            <p class="text-sm text-muted">You haven't issued any prescriptions yet.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px]">
                    <thead>
                        <tr>
                            <th class="table-head">Date</th>
                            <th class="table-head">Patient</th>
                            <th class="table-head">Diagnosis</th>
                            <th class="table-head">Medications</th>
                            <th class="table-head">Follow-up</th>
                            <th class="table-head text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @foreach ($prescriptions as $prescription)
                            <tr>
                                <td class="table-cell text-muted">{{ $prescription->created_at->format('d M Y') }}</td>
                                <td class="table-cell">
                                    <a href="{{ route('doctor.patients.show', $prescription->patient) }}" class="font-medium hover:text-brand-700">
                                        {{ $prescription->patient->name }}
                                    </a>
                                </td>
                                <td class="table-cell text-muted max-w-xs truncate">{{ $prescription->diagnosis }}</td>
                                <td class="table-cell">
                                    <span class="badge bg-brand-100 text-brand-700">{{ $prescription->items->count() }} items</span>
                                </td>
                                <td class="table-cell text-muted">{{ $prescription->follow_up_date?->format('d M Y') ?? '—' }}</td>
                                <td class="table-cell text-right">
                                    <a href="{{ route('doctor.prescriptions.show', $prescription) }}" class="btn-outline !px-3 !py-1 !text-xs">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.staff>
