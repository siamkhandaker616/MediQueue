<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class VisitHistoryController extends Controller
{
    /**
     * FR-11: Patient Visit History Log
     */
    public function index(Request $request)
    {
        $patientId = auth()->id() ?? 1;
        $tab = $request->query('tab', 'all');

        $query = Appointment::where('patient_id', $patientId)
            ->with(['doctor.user', 'department', 'payment', 'prescription', 'medicalReports'])
            ->orderByDesc('date')
            ->orderByDesc('time_slot');

        if ($tab === 'upcoming') {
            $query->where('date', '>=', now()->toDateString())
                  ->whereIn('status', [Appointment::STATUS_SCHEDULED, Appointment::STATUS_CHECKED_IN]);
        } elseif ($tab === 'completed') {
            $query->where('status', Appointment::STATUS_COMPLETED);
        } elseif ($tab === 'cancelled') {
            $query->where('status', Appointment::STATUS_CANCELLED);
        }

        $appointments = $query->paginate(8)->withQueryString();

        $stats = [
            'total'     => Appointment::where('patient_id', $patientId)->count(),
            'upcoming'  => Appointment::where('patient_id', $patientId)->where('date', '>=', now()->toDateString())->whereIn('status', [Appointment::STATUS_SCHEDULED, Appointment::STATUS_CHECKED_IN])->count(),
            'completed' => Appointment::where('patient_id', $patientId)->where('status', Appointment::STATUS_COMPLETED)->count(),
            'cancelled' => Appointment::where('patient_id', $patientId)->where('status', Appointment::STATUS_CANCELLED)->count(),
        ];

        return view('patient.history', compact('appointments', 'tab', 'stats'));
    }
}