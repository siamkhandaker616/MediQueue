<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * FR-03: 4-Step Smart Booking Wizard
     */
    public function create(Request $request)
    {
        $departments = Department::active()->with(['activeDoctors.user'])->orderBy('name')->get();
        $selectedDoctor = null;
        $selectedDepartment = null;

        if ($request->filled('doctor')) {
            $selectedDoctor = Doctor::where('slug', $request->query('doctor'))
                ->orWhere('id', $request->query('doctor'))
                ->with(['department', 'user'])
                ->first();
            if ($selectedDoctor) {
                $selectedDepartment = $selectedDoctor->department;
            }
        } elseif ($request->filled('department')) {
            $selectedDepartment = Department::where('slug', $request->query('department'))->first();
        }

        return view('appointments.create', compact('departments', 'selectedDoctor', 'selectedDepartment'));
    }

    /**
     * AJAX Endpoint: Fetch real-time available slots for doctor & date (accepts Slug or ID)
     */
    public function getSlots(Request $request, $doctor)
    {
        $doc = is_numeric($doctor)
            ? Doctor::find($doctor)
            : Doctor::where('slug', $doctor)->orWhere('id', $doctor)->first();

        if (!$doc) {
            $doc = Doctor::firstOrFail();
        }

        $date = $request->query('date', now()->toDateString());
        $slotData = DoctorSchedule::getSlotsForDoctorAndDate($doc, $date);

        return response()->json($slotData);
    }

    /**
     * Store Appointment & Forward to Payment Checkout (FR-03, FR-04 & FR-07)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date'      => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
            'symptoms'  => 'nullable|string|max:500',
        ]);

        $doctor = Doctor::with('department')->findOrFail($validated['doctor_id']);

        // Check if slot was booked in the last moments
        $isTaken = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', $validated['date'])
            ->where('time_slot', $validated['time_slot'])
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED])
            ->exists();

        if ($isTaken) {
            return back()->withErrors(['time_slot' => 'This slot was just booked by another patient. Please pick another slot.'])->withInput();
        }

        $appointment = DB::transaction(function () use ($validated, $doctor) {
            $countToday = Appointment::where('doctor_id', $doctor->id)
                ->whereDate('date', $validated['date'])
                ->count();

            $queuePos = $countToday + 1;
            $deptPrefix = strtoupper(substr($doctor->department->slug ?? 'GEN', 0, 4));
            $dateCode = date('md', strtotime($validated['date']));
            $token = sprintf('TK-%s%s-%03d', $deptPrefix, $dateCode, $queuePos);
            $estWait = ($queuePos - 1) * 15;

            return Appointment::create([
                'patient_id'             => auth()->id() ?? 1,
                'doctor_id'              => $doctor->id,
                'department_id'          => $doctor->department_id,
                'date'                   => $validated['date'],
                'time_slot'              => $validated['time_slot'],
                'status'                 => Appointment::STATUS_SCHEDULED,
                'token_number'           => $token,
                'queue_position'         => $queuePos,
                'estimated_wait_minutes' => $estWait,
                'fee'                    => $doctor->consultation_fee,
                'payment_status'         => 'unpaid', // Initial unpaid status
                'symptoms'               => $validated['symptoms'] ?? null,
            ]);
        });

        // Redirect directly to SSLCommerz / bKash / Nagad payment selection page
        return redirect()->route('payments.checkout', $appointment)
            ->with('success', 'Appointment slot reserved! Please select a payment method to complete booking.');
    }

    /**
     * FR-04: Digital Queue Token Card
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['doctor.user', 'department', 'patient', 'payment']);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * FR-06: Reschedule Appointment Page
     */
    public function reschedule(Appointment $appointment)
    {
        abort_unless($appointment->canBeRescheduled(), 403, 'This appointment cannot be rescheduled.');

        $appointment->load(['doctor.user', 'department']);

        return view('appointments.reschedule', compact('appointment'));
    }

    /**
     * FR-06: Process Reschedule Update
     */
    public function updateSchedule(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->canBeRescheduled(), 403, 'This appointment cannot be rescheduled.');

        $validated = $request->validate([
            'date'      => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
        ]);

        $doctor = $appointment->doctor;

        // Check if new slot is taken
        $isTaken = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', $validated['date'])
            ->where('time_slot', $validated['time_slot'])
            ->where('id', '!=', $appointment->id)
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED])
            ->exists();

        if ($isTaken) {
            return back()->withErrors(['time_slot' => 'The selected slot is already booked. Please choose another slot.'])->withInput();
        }

        DB::transaction(function () use ($appointment, $validated, $doctor) {
            $countToday = Appointment::where('doctor_id', $doctor->id)
                ->whereDate('date', $validated['date'])
                ->where('id', '!=', $appointment->id)
                ->count();

            $queuePos = $countToday + 1;
            $deptPrefix = strtoupper(substr($doctor->department->slug ?? 'GEN', 0, 4));
            $dateCode = date('md', strtotime($validated['date']));
            $token = sprintf('TK-%s%s-%03d', $deptPrefix, $dateCode, $queuePos);
            $estWait = ($queuePos - 1) * 15;

            $appointment->update([
                'date'                   => $validated['date'],
                'time_slot'              => $validated['time_slot'],
                'token_number'           => $token,
                'queue_position'         => $queuePos,
                'estimated_wait_minutes' => $estWait,
                'status'                 => Appointment::STATUS_SCHEDULED,
            ]);
        });

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Your appointment was successfully rescheduled! Token and queue position updated.');
    }

    /**
     * FR-10: Cancel Appointment
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->canBeCancelled(), 403, 'This appointment cannot be cancelled.');

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        $appointment->update([
            'status'              => Appointment::STATUS_CANCELLED,
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('info', 'Appointment cancelled. Your refund has been initiated based on our cancellation policy.');
    }

    public function index()
    {
        return redirect()->route('patient.history');
    }
}