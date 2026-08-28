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
     * FR-03: Smart Appointment Booking Wizard (Step 1-4)
     */
    public function create(Request $request)
    {
        $departments = Department::active()->with(['activeDoctors.user'])->orderBy('name')->get();
        $selectedDoctor = null;
        $selectedDepartment = null;

        if ($request->filled('doctor')) {
            $selectedDoctor = Doctor::where('slug', $request->query('doctor'))
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
     * AJAX Endpoint: Fetch real-time available slots (Accepts either ID or Slug)
     */
    public function getSlots(Request $request, $doctor)
    {
        // Find doctor by ID or Slug seamlessly
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
     * FR-03 & FR-04: Store Appointment, generate unique token, prevent double-booking.
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

        // Check if slot was taken in the last moments (Concurrency protection)
        $isTaken = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', $validated['date'])
            ->where('time_slot', $validated['time_slot'])
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED])
            ->exists();

        if ($isTaken) {
            return back()->withErrors(['time_slot' => 'Sorry, this time slot was just booked by another patient. Please choose another slot.'])->withInput();
        }

        // Generate token and queue details inside a transaction
        $appointment = DB::transaction(function () use ($validated, $doctor) {
            $countToday = Appointment::where('doctor_id', $doctor->id)
                ->whereDate('date', $validated['date'])
                ->count();

            $queuePos = $countToday + 1;
            $deptPrefix = strtoupper(substr($doctor->department->slug ?? 'GEN', 0, 4));
            $dateCode = date('md', strtotime($validated['date']));
            $tokenNumber = sprintf('TK-%s%s-%03d', $deptPrefix, $dateCode, $queuePos);
            $estimatedWait = ($queuePos - 1) * 15;

            return Appointment::create([
                'patient_id'             => auth()->id() ?? 1,
                'doctor_id'              => $doctor->id,
                'department_id'          => $doctor->department_id,
                'date'                   => $validated['date'],
                'time_slot'              => $validated['time_slot'],
                'token_number'           => $tokenNumber,
                'queue_position'         => $queuePos,
                'estimated_wait_minutes' => $estimatedWait,
                'fee'                    => $doctor->consultation_fee,
                'status'                 => Appointment::STATUS_SCHEDULED,
                'payment_status'         => 'pending',
                'symptoms'               => $validated['symptoms'] ?? null,
            ]);
        });

        return redirect()->route('payments.checkout', $appointment)
            ->with('success', 'Appointment slot reserved! Please select a payment method to complete booking.');
    }

    /**
     * FR-04: Digital Queue Token Card Page.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['doctor.user', 'department', 'patient']);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Patient's appointment history list.
     */
    public function index()
    {
        $appointments = Appointment::where('patient_id', auth()->id() ?? 1)
            ->with(['doctor.user', 'department'])
            ->orderByDesc('date')
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }
}