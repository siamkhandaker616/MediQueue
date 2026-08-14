<x-layouts.staff>
    <x-slot name="title">Edit {{ $doctor->name }}</x-slot>

    @php
        $specialties = is_array($doctor->specialties) ? implode(', ', $doctor->specialties) : $doctor->specialties;
        $languages = is_array($doctor->languages) ? implode(', ', $doctor->languages) : $doctor->languages;
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">Admin · doctor management</p>
            <h1 class="text-2xl font-bold tracking-tight">Edit {{ $doctor->name }}</h1>
        </div>
        <a href="{{ route('admin.doctors.index') }}" class="btn-outline">Back to doctors</a>
    </div>

    <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" enctype="multipart/form-data" class="card max-w-3xl p-6">
        @csrf
        @method('PATCH')

        <h2 class="font-semibold">Account &amp; profile</h2>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="name" class="label">Full name <span class="text-red-500">*</span></label>
                <input id="name" name="name" value="{{ old('name', $doctor->name) }}" class="input mt-1" required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="label">Email <span class="text-red-500">*</span></label>
                <input id="email" name="email" type="email" value="{{ old('email', $doctor->email) }}" class="input mt-1" required>
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="label">New password</label>
                <input id="password" name="password" type="password" class="input mt-1">
                <p class="mt-1 text-xs text-muted">Leave blank to keep the current password.</p>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="photo" class="label">Profile photo</label>
                <input id="photo" name="photo" type="file" accept="image/*" class="input mt-1">
                @if ($doctor->photo)
                    <p class="mt-1 text-xs text-muted">Leave blank to keep the current photo.</p>
                @endif
                @error('photo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <h2 class="mt-8 font-semibold">Practice details</h2>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="department_id" class="label">Department <span class="text-red-500">*</span></label>
                <select id="department_id" name="department_id" class="input mt-1" required>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id', $doctor->department_id) == $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="qualifications" class="label">Qualifications <span class="text-red-500">*</span></label>
                <input id="qualifications" name="qualifications" value="{{ old('qualifications', $doctor->qualifications) }}" class="input mt-1" required>
                @error('qualifications')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="specialties" class="label">Specialties</label>
                <input id="specialties" name="specialties" value="{{ old('specialties', $specialties) }}" class="input mt-1">
                <p class="mt-1 text-xs text-muted">Comma separated.</p>
            </div>
            <div>
                <label for="languages" class="label">Languages</label>
                <input id="languages" name="languages" value="{{ old('languages', $languages) }}" class="input mt-1">
                <p class="mt-1 text-xs text-muted">Comma separated.</p>
            </div>
            <div>
                <label for="experience_years" class="label">Years of experience</label>
                <input id="experience_years" name="experience_years" type="number" min="0" value="{{ old('experience_years', $doctor->experience_years) }}" class="input mt-1">
            </div>
            <div>
                <label for="consultation_fee" class="label">Consultation fee (৳) <span class="text-red-500">*</span></label>
                <input id="consultation_fee" name="consultation_fee" type="number" step="0.01" min="0" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" class="input mt-1" required>
                @error('consultation_fee')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <label for="bio" class="label">Short bio</label>
            <textarea id="bio" name="bio" rows="3" class="input mt-1">{{ old('bio', $doctor->bio) }}</textarea>
        </div>

        <label class="mt-4 flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked($doctor->is_active) class="rounded border-brand-200 text-brand-600 focus:ring-brand-500">
            Active (available for appointments)
        </label>

        <div class="mt-8 flex items-center justify-end gap-3">
            <a href="{{ route('admin.doctors.index') }}" class="btn-outline">Cancel</a>
            <button class="btn-primary">Save changes</button>
        </div>
    </form>
</x-layouts.staff>
