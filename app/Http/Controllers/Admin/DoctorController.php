<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
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

    public function toggleActive(Doctor $doctor): RedirectResponse
    {
        $doctor->update(['is_active' => ! $doctor->is_active]);

        return back()->with('status', $doctor->is_active ? 'Doctor re-activated.' : 'Doctor deactivated.');
    }
}
