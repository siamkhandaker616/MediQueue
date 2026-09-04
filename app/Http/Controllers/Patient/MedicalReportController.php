<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalReportController extends Controller
{
    /**
     * FR-12: List Patient Reports
     */
    public function index()
    {
        $patientId = auth()->id() ?? 1;

        $reports = MedicalReport::where('patient_id', $patientId)
            ->with(['appointment.doctor.user', 'appointment.department'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $appointments = Appointment::where('patient_id', $patientId)
            ->with('doctor.user')
            ->orderByDesc('date')
            ->get();

        return view('patient.reports.index', compact('reports', 'appointments'));
    }

    /**
     * FR-12: Upload New Medical Report
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:150',
            'report_type'    => 'required|in:lab,radiology,prescription,cardiac,other',
            'appointment_id' => 'nullable|exists:appointments,id',
            'report_date'    => 'nullable|date',
            'file'           => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
            'notes'          => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filePath = $file->store('medical-reports/' . (auth()->id() ?? 1), 'public');

        MedicalReport::create([
            'patient_id'     => auth()->id() ?? 1,
            'appointment_id' => $request->appointment_id,
            'title'          => $request->title,
            'report_type'    => $request->report_type,
            'file_path'      => $filePath,
            'file_name'      => $originalName,
            'file_size'      => $file->getSize(),
            'mime_type'      => $file->getClientMimeType(),
            'report_date'    => $request->report_date ?? now()->toDateString(),
            'notes'          => $request->notes,
            'status'         => 'uploaded',
        ]);

        return back()->with('success', 'Medical report uploaded successfully!');
    }

    /**
     * FR-12: Delete Report
     */
    public function destroy(MedicalReport $report)
    {
        abort_if($report->patient_id !== (auth()->id() ?? 1), 403);

        Storage::disk('public')->delete($report->file_path);
        $report->delete();

        return back()->with('success', 'Report deleted successfully.');
    }
}