<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Landing page with live departments, doctors, and platform stats.
     */
    public function index(): View
    {
        $departments = Department::active()
            ->withCount('activeDoctors')
            ->orderBy('name')
            ->limit(6)
            ->get();

        $doctors = Doctor::active()
            ->with('department')
            ->orderByDesc('avg_rating')
            ->orderByDesc('rating_count')
            ->limit(6)
            ->get();

        $stats = [
            'departments'  => Department::active()->count(),
            'doctors'      => Doctor::active()->count(),
            'patients'     => User::where('role', 'patient')->count(),
            'appointments' => Appointment::count(),
        ];

        return view('home', compact('departments', 'doctors', 'stats'));
    }
}
