<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * FR-19: Show Rating & Review Form
     */
    public function create(Appointment $appointment)
    {
        $patientId = auth()->id() ?? 1;
        abort_if($appointment->patient_id !== $patientId, 403);
        abort_if($appointment->status !== Appointment::STATUS_COMPLETED, 400, 'Reviews can only be submitted for completed consultations.');

        $existingReview = Review::where('appointment_id', $appointment->id)->first();
        if ($existingReview) {
            return redirect()->route('patient.history')
                ->with('info', 'You have already reviewed this consultation. Thank you!');
        }

        $appointment->load(['doctor.user', 'department']);

        return view('patient.reviews.create', compact('appointment'));
    }

    /**
     * FR-19: Store Review & Recalculate Doctor's Rating
     */
    public function store(Request $request, Appointment $appointment)
    {
        $patientId = auth()->id() ?? 1;
        abort_if($appointment->patient_id !== $patientId, 403);
        abort_if($appointment->status !== Appointment::STATUS_COMPLETED, 400);

        $validated = $request->validate([
            'rating'       => 'required|integer|min:1|max:5',
            'comment'      => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($appointment, $patientId, $validated) {
            Review::create([
                'appointment_id' => $appointment->id,
                'doctor_id'      => $appointment->doctor_id,
                'patient_id'     => $patientId,
                'rating'         => $validated['rating'],
                'comment'        => $validated['comment'] ?? null,
                'is_anonymous'   => $validated['is_anonymous'] ?? false,
            ]);

            // Recalculate doctor's overall average rating & total reviews
            $doctor = $appointment->doctor;
            $avgRating = Review::where('doctor_id', $doctor->id)->avg('rating');
            $ratingCount = Review::where('doctor_id', $doctor->id)->count();

            $doctor->update([
                'avg_rating'   => round($avgRating, 2),
                'rating_count' => $ratingCount,
            ]);
        });

        return redirect()->route('patient.history')
            ->with('success', 'Thank you for your feedback! Your review has been submitted.');
    }
}