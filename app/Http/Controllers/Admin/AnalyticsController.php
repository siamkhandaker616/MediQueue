<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::count();
        $revenue = (float) Payment::where('status', Payment::STATUS_PAID)->sum('amount');
        $cancelled = Appointment::where('status', Appointment::STATUS_CANCELLED)->count();
        $cancellationRate = $appointments > 0 ? round($cancelled / $appointments * 100, 1) : 0;

        $byDepartment = Appointment::query()
            ->selectRaw('department_id, count(*) as total')
            ->with('department:id,name')
            ->groupBy('department_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->department->name ?? 'Unknown',
                'count' => $row->total,
            ]);

        $peakHours = Appointment::query()
            ->selectRaw("substr(time_slot, 1, 2) as hour, count(*) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->hour.':00',
                'count' => $row->total,
            ]);

        $statusBreakdown = Appointment::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $weeklyRevenue = collect(range(6, 0))->map(function ($daysAgo) {
            $day = now()->subDays($daysAgo)->toDateString();

            return [
                'label' => now()->subDays($daysAgo)->format('D'),
                'total' => Payment::where('status', Payment::STATUS_PAID)->whereDate('paid_at', $day)->sum('amount'),
            ];
        });

        $doctors = Doctor::query()
            ->with('department:id,name')
            ->withCount('appointments')
            ->get()
            ->map(function (Doctor $doctor) {
                $revenue = Payment::where('status', Payment::STATUS_PAID)
                    ->whereHas('appointment', fn ($q) => $q->where('doctor_id', $doctor->id))
                    ->sum('amount');
                $rating = Review::where('doctor_id', $doctor->id)->where('is_visible', true)->avg('overall_rating');

                return [
                    'name' => $doctor->name,
                    'department' => $doctor->department->name ?? '—',
                    'appointments' => $doctor->appointments_count,
                    'rating' => $rating ? round((float) $rating, 1) : null,
                    'revenue' => (float) $revenue,
                ];
            })
            ->sortByDesc('appointments')
            ->take(8)
            ->values();

        return view('admin.analytics', compact(
            'appointments',
            'revenue',
            'cancellationRate',
            'byDepartment',
            'peakHours',
            'statusBreakdown',
            'weeklyRevenue',
            'doctors',
        ));
    }
}
