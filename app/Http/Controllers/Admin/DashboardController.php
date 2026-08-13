<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->today();

        return view('admin.dashboard', [
            'todayAppointments' => Appointment::whereDate('date', $today)->count(),
            'todayRevenue' => Payment::where('status', Payment::STATUS_PAID)->whereDate('paid_at', $today)->sum('amount'),
            'totalPatients' => User::where('role', 'patient')->count(),
            'totalDoctors' => Doctor::count(),
            'totalAppointments' => Appointment::count(),
            'totalRevenue' => Payment::where('status', Payment::STATUS_PAID)->sum('amount'),
            'pendingReviews' => Review::where('is_visible', false)->count(),
            'recentAppointments' => Appointment::with(['patient', 'doctor', 'department'])
                ->latest('date')
                ->take(8)
                ->get(),
        ]);
    }
}
