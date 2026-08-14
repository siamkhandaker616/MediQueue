<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function index(): View
    {
        $doctor = $this->doctor();

        $appointments = $doctor->appointments()
            ->with(['patient', 'department'])
            ->whereDate('date', now()->today())
            ->orderBy('time_slot')
            ->get();

        $waiting = $appointments->whereIn('status', [Appointment::STATUS_SCHEDULED, Appointment::STATUS_CHECKED_IN])->count();
        $inProgress = $appointments->where('status', Appointment::STATUS_IN_PROGRESS)->count();
        $served = $appointments->where('status', Appointment::STATUS_COMPLETED)->count();
        $nowServing = $appointments->where('status', Appointment::STATUS_IN_PROGRESS)->first();
        $nextUp = $appointments->whereIn('status', [Appointment::STATUS_CHECKED_IN, Appointment::STATUS_SCHEDULED])->first();

        return view('doctor.queue', compact('doctor', 'appointments', 'waiting', 'inProgress', 'served', 'nowServing', 'nextUp'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeAppointment($appointment);

        $data = $request->validate([
            'status' => ['required', 'in:scheduled,checked_in,in_progress,completed,cancelled,no_show'],
        ]);

        $appointment->update(['status' => $data['status']]);

        return back()->with('status', 'Queue updated.');
    }

    private function authorizeAppointment(Appointment $appointment): void
    {
        abort_unless($appointment->doctor_id === $this->doctor()->id, 403);
    }

    private function doctor()
    {
        return auth()->user()->doctor;
    }
}
