<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * FR-18: List Patient Prescriptions
     */
    public function index()
    {
        $patientId = auth()->id() ?? 1;

        $prescriptions = Prescription::where('patient_id', $patientId)
            ->with(['doctor.user', 'doctor.department', 'appointment', 'items'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('patient.prescriptions.index', compact('prescriptions'));
    }

    /**
     * FR-18: View Digital Rx Prescription & Print
     */
    public function show(Prescription $prescription)
    {
        $patientId = auth()->id() ?? 1;
        abort_if($prescription->patient_id !== $patientId, 403, 'Unauthorized access to prescription.');

        $prescription->load(['doctor.user', 'doctor.department', 'patient', 'appointment', 'items']);

        return view('patient.prescriptions.show', compact('prescription'));
    }
}