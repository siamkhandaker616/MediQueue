<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    public function index(): View
    {
        $prescriptions = auth()->user()->doctor->prescriptions()
            ->with(['patient', 'appointment', 'items'])
            ->latest()
            ->get();

        return view('doctor.prescriptions.index', compact('prescriptions'));
    }

    public function create(Appointment $appointment): View
    {
        $this->authorizeAppointment($appointment);

        return view('doctor.prescriptions.compose', compact('appointment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $appointment = Appointment::findOrFail($data['appointment_id']);
        $this->authorizeAppointment($appointment);

        $prescription = auth()->user()->doctor->prescriptions()->create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'diagnosis' => $data['diagnosis'],
            'investigation' => $data['investigation'] ?? null,
            'follow_up_date' => $data['follow_up_date'] ?? null,
            'dietary_advice' => $data['dietary_advice'] ?? null,
            'doctor_notes' => $data['doctor_notes'] ?? null,
            'is_editable' => true,
        ]);

        foreach ($data['items'] as $item) {
            $prescription->items()->create($item);
        }

        $appointment->update(['status' => Appointment::STATUS_COMPLETED]);

        return redirect()->route('doctor.prescriptions.show', $prescription)
            ->with('status', 'Prescription saved and appointment marked complete.');
    }

    public function edit(Prescription $prescription): View
    {
        $this->authorizePrescription($prescription);
        $this->authorizeEditable($prescription);

        return view('doctor.prescriptions.edit', compact('prescription'));
    }

    public function update(Request $request, Prescription $prescription): RedirectResponse
    {
        $this->authorizePrescription($prescription);
        $this->authorizeEditable($prescription);

        $data = $this->validated($request);

        $prescription->update([
            'diagnosis' => $data['diagnosis'],
            'investigation' => $data['investigation'] ?? null,
            'follow_up_date' => $data['follow_up_date'] ?? null,
            'dietary_advice' => $data['dietary_advice'] ?? null,
            'doctor_notes' => $data['doctor_notes'] ?? null,
        ]);

        $prescription->items()->delete();

        foreach ($data['items'] as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('doctor.prescriptions.show', $prescription)
            ->with('status', 'Prescription updated.');
    }

    public function show(Prescription $prescription): View
    {
        $this->authorizePrescription($prescription);

        return view('doctor.prescriptions.show', compact('prescription'));
    }

    public function pdf(Prescription $prescription): \Illuminate\Http\Response
    {
        $this->authorizePrescription($prescription);

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('doctor.prescriptions.pdf', compact('prescription'))
            ->download('prescription-'.$prescription->id.'.pdf');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'diagnosis' => ['required', 'string'],
            'investigation' => ['nullable', 'string'],
            'follow_up_date' => ['nullable', 'date', 'after:today'],
            'dietary_advice' => ['nullable', 'string'],
            'doctor_notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_name' => ['required', 'string'],
            'items.*.dosage' => ['required', 'string'],
            'items.*.frequency' => ['required', 'string'],
            'items.*.duration' => ['nullable', 'string'],
            'items.*.instructions' => ['nullable', 'string'],
        ]);
    }

    private function authorizeEditable(Prescription $prescription): void
    {
        $graceMinutes = (int) config('mediqueue.prescription_edit_grace_minutes', 60);

        abort_unless(
            $prescription->is_editable && $prescription->created_at->gte(now()->subMinutes($graceMinutes)),
            403,
            'Prescriptions can only be edited within the grace period.',
        );
    }

    private function authorizeAppointment(Appointment $appointment): void
    {
        abort_unless($appointment->doctor_id === auth()->user()->doctor->id, 403);
    }

    private function authorizePrescription(Prescription $prescription): void
    {
        abort_unless($prescription->doctor_id === auth()->user()->doctor->id, 403);
    }
}
