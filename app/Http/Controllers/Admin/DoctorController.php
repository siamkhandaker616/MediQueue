<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        $doctors = Doctor::with(['department', 'user'])
            ->withCount('appointments')
            ->get()
            ->each(function (Doctor $doctor) {
                $doctor->revenue = Payment::where('status', Payment::STATUS_PAID)
                    ->whereHas('appointment', fn ($q) => $q->where('doctor_id', $doctor->id))
                    ->sum('amount');
                $doctor->rating = Review::where('doctor_id', $doctor->id)->where('is_visible', true)->avg('overall_rating');
            });

        return view('admin.doctors', compact('doctors'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.doctors.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);

        $password = $data['password'] ?? Str::random(10);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $password,
            'role' => 'doctor',
        ]);

        $user->doctor()->create([
            'department_id' => $data['department_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'photo' => $this->uploadPhoto($request),
            'qualifications' => $data['qualifications'],
            'specialties' => $this->splitList($data['specialties'] ?? null),
            'experience_years' => $data['experience_years'] ?? 0,
            'consultation_fee' => $data['consultation_fee'],
            'languages' => $this->splitList($data['languages'] ?? null),
            'bio' => $data['bio'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.doctors.index')
            ->with('status', "Doctor added. Sign-in credentials — email: {$data['email']}, password: {$password}");
    }

    public function edit(Doctor $doctor): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.doctors.edit', compact('doctor', 'departments'));
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $data = $this->validated($request, $doctor->user_id);

        $doctor->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (filled($data['password'] ?? null)) {
            $doctor->user->update(['password' => $data['password']]);
        }

        $doctor->update([
            'department_id' => $data['department_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'photo' => $this->uploadPhoto($request) ?? $doctor->photo,
            'qualifications' => $data['qualifications'],
            'specialties' => $this->splitList($data['specialties'] ?? null),
            'experience_years' => $data['experience_years'] ?? 0,
            'consultation_fee' => $data['consultation_fee'],
            'languages' => $this->splitList($data['languages'] ?? null),
            'bio' => $data['bio'] ?? null,
            'is_active' => $request->boolean('is_active', $doctor->is_active),
        ]);

        return back()->with('status', 'Doctor updated.');
    }

    public function toggleActive(Doctor $doctor): RedirectResponse
    {
        $doctor->update(['is_active' => ! $doctor->is_active]);

        return back()->with('status', $doctor->is_active ? 'Doctor re-activated.' : 'Doctor deactivated.');
    }

    private function validated(Request $request, ?int $ignoreUserId): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoreUserId)],
            'password' => ['nullable', 'string', 'min:8'],
            'department_id' => ['required', 'exists:departments,id'],
            'qualifications' => ['required', 'string', 'max:255'],
            'specialties' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'languages' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function splitList(?string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $value ?? ''))));
    }

    private function uploadPhoto(Request $request): ?string
    {
        return $request->hasFile('photo')
            ? $request->file('photo')->store('doctor-photos', 'public')
            : null;
    }
}
