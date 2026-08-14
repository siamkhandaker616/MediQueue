<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function show(User $patient): View
    {
        abort_unless($patient->role === 'patient', 404);

        $doctor = auth()->user()->doctor;

        abort_unless($this->isTreatingDoctor($doctor, $patient), 403);

        $profile = $patient->medicalProfile;
        $appointments = $patient->appointments()
            ->with(['doctor', 'department'])
            ->where('doctor_id', $doctor->id)
            ->latest('date')
            ->get();

        $prescriptions = Prescription::with(['appointment', 'items'])
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->get();

        return view('doctor.patients.show', compact('patient', 'profile', 'appointments', 'prescriptions'));
    }

    private function isTreatingDoctor(Doctor $doctor, User $patient): bool
    {
        $hasAppointment = $patient->appointments()->where('doctor_id', $doctor->id)->exists();

        $hasPrescription = Prescription::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->exists();

        return $hasAppointment || $hasPrescription;
    }
}
