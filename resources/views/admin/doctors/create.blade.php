<x-layouts.staff>
    <x-slot name="title">Add doctor</x-slot>

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">Admin · doctor management</p>
            <h1 class="text-2xl font-bold tracking-tight">Add doctor</h1>
        </div>
        <a href="{{ route('admin.doctors.index') }}" class="btn-outline">Back to doctors</a>
    </div>

    <form method="POST" action="{{ route('admin.doctors.store') }}" enctype="multipart/form-data" class="card max-w-3xl p-6">
        @csrf

        <h2 class="font-semibold">Account &amp; profile</h2>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="name" class="label">Full name <span class="text-red-500">*</span></label>
                <input id="name" name="name" value="{{ old('name') }}" class="input mt-1" required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="label">Email <span class="text-red-500">*</span></label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input mt-1" required>
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password" class="input mt-1">
                <p class="mt-1 text-xs text-muted">Leave blank to auto-generate sign-in credentials.</p>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="photo" class="label">Profile photo</label>
                <input id="photo" name="photo" type="file" accept="image/*" class="input mt-1">
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
                    <option value="">Select department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
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
                <input id="qualifications" name="qualifications" value="{{ old('qualifications') }}" placeholder="MBBS, FCPS" class="input mt-1" required>
                @error('qualifications')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="specialties" class="label">Specialties</label>
                <input id="specialties" name="specialties" value="{{ old('specialties') }}" placeholder="Cardiology, Interventional" class="input mt-1">
                <p class="mt-1 text-xs text-muted">Comma separated.</p>
            </div>
            <div>
                <label for="languages" class="label">Languages</label>
                <input id="languages" name="languages" value="{{ old('languages') }}" placeholder="Bengali, English" class="input mt-1">
                <p class="mt-1 text-xs text-muted">Comma separated.</p>
            </div>
            <div>
                <label for="experience_years" class="label">Years of experience</label>
                <input id="experience_years" name="experience_years" type="number" min="0" value="{{ old('experience_years') }}" class="input mt-1">
            </div>
            <div>
                <label for="consultation_fee" class="label">Consultation fee (৳) <span class="text-red-500">*</span></label>
                <input id="consultation_fee" name="consultation_fee" type="number" step="0.01" min="0" value="{{ old('consultation_fee') }}" class="input mt-1" required>
                @error('consultation_fee')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <label for="bio" class="label">Short bio</label>
            <textarea id="bio" name="bio" rows="3" class="input mt-1">{{ old('bio') }}</textarea>
        </div>

        <label class="mt-4 flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-brand-200 text-brand-600 focus:ring-brand-500">
            Active (available for appointments)
        </label>

        <div class="mt-8 flex items-center justify-end gap-3">
            <a href="{{ route('admin.doctors.index') }}" class="btn-outline">Cancel</a>
            <button class="btn-primary">Add doctor</button>
        </div>
    </form>
</x-layouts.staff>
