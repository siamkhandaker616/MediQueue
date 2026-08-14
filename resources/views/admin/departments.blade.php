<x-layouts.staff>
    <x-slot name="title">Departments</x-slot>

    <div class="mb-6">
        <p class="text-sm text-muted">Admin · department management</p>
        <h1 class="text-2xl font-bold tracking-tight">Departments</h1>
    </div>

    <div class="card p-5">
        <h2 class="font-semibold">Add department</h2>
        <form method="POST" action="{{ route('admin.departments.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @csrf
            <div class="lg:col-span-1">
                <label for="name" class="label">Name</label>
                <input id="name" name="name" value="{{ old('name') }}" class="input mt-1" required>
            </div>
            <div class="lg:col-span-1">
                <label for="fee_range" class="label">Fee range</label>
                <input id="fee_range" name="fee_range" value="{{ old('fee_range') }}" placeholder="500 - 800" class="input mt-1" required>
            </div>
            <div class="lg:col-span-1">
                <label for="floor_number" class="label">Floor</label>
                <input id="floor_number" name="floor_number" type="number" value="{{ old('floor_number') }}" class="input mt-1">
            </div>
            <div class="lg:col-span-1">
                <label for="room_number" class="label">Room</label>
                <input id="room_number" name="room_number" value="{{ old('room_number') }}" class="input mt-1">
            </div>
            <div class="flex items-end">
                <button class="btn-primary w-full">Add</button>
            </div>
            <div class="lg:col-span-5">
                <label for="description" class="label">Description</label>
                <input id="description" name="description" value="{{ old('description') }}" class="input mt-1">
            </div>
            @error('name')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </form>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-brand-100 px-5 py-4">
            <h2 class="font-semibold">All departments</h2>
        </div>
        @if ($departments->isEmpty())
            <p class="px-5 py-12 text-center text-sm text-muted">No departments yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px]">
                    <thead>
                        <tr>
                            <th class="table-head">Name</th>
                            <th class="table-head">Location</th>
                            <th class="table-head">Fee range</th>
                            <th class="table-head">Doctors</th>
                            <th class="table-head">Appointments</th>
                            <th class="table-head text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @foreach ($departments as $department)
                            <tr x-data="{ editing: false }">
                                <td class="table-cell">
                                    <template x-if="!editing">
                                        <span class="font-medium">{{ $department->name }}</span>
                                    </template>
                                    <template x-if="editing">
                                        <form method="POST" action="{{ route('admin.departments.update', $department) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input name="name" value="{{ $department->name }}" class="input !py-1 !text-sm" required>
                                            <input name="fee_range" value="{{ $department->fee_range }}" class="input !py-1 !text-sm" required>
                                            <input name="floor_number" value="{{ $department->floor_number }}" class="input !py-1 !text-sm" placeholder="Floor">
                                            <input name="room_number" value="{{ $department->room_number }}" class="input !py-1 !text-sm" placeholder="Room">
                                            <input name="description" value="{{ $department->description }}" class="input !py-1 !text-sm" placeholder="Description">
                                            <button class="btn-primary !px-3 !py-1 !text-xs">Save</button>
                                        </form>
                                    </template>
                                </td>
                                <td class="table-cell text-muted">{{ $department->floor_number ? 'Floor '.$department->floor_number.', Room '.$department->room_number : '—' }}</td>
                                <td class="table-cell text-muted">৳{{ $department->fee_range }}</td>
                                <td class="table-cell"><span class="badge bg-brand-100 text-brand-700">{{ $department->doctors_count }}</span></td>
                                <td class="table-cell text-muted">{{ $department->appointments_count }}</td>
                                <td class="table-cell">
                                    <div class="flex justify-end gap-2">
                                        <button @click="editing = !editing" class="btn-outline !px-3 !py-1 !text-xs" x-text="editing ? 'Cancel' : 'Edit'"></button>
                                        <form method="POST" action="{{ route('admin.departments.destroy', $department) }}"
                                              onsubmit="return confirm('Delete this department?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-outline !px-3 !py-1 !text-xs !text-red-600 hover:!bg-red-50">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.staff>
