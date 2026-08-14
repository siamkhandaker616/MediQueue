<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $doctor = auth()->user()->doctor;

        $schedules = $doctor->schedules()->orderBy('day_of_week')->orderBy('start_time')->get();
        $leaves = $doctor->leaves()->orderByDesc('date')->get();

        return view('doctor.schedule', compact('schedules', 'leaves'));
    }

    public function storeLeave(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->doctor->leaves()->create($data);

        return back()->with('status', 'Leave requested.');
    }
}
