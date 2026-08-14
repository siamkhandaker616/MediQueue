<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.analytics', $this->metrics($request));
    }

    public function print(Request $request): View
    {
        return view('admin.analytics-print', $this->metrics($request));
    }

    public function export(Request $request): StreamedResponse
    {
        [$start, $end] = $this->resolveRange($request);

        $payments = Payment::query()
            ->with(['appointment.patient', 'appointment.doctor', 'appointment.department'])
            ->whereBetween('paid_at', [$start, $end])
            ->orderBy('paid_at');

        $filename = 'analytics-'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Receipt', 'Patient', 'Doctor', 'Department', 'Paid at', 'Method', 'Amount', 'Status']);

            $payments->chunk(200, function (Collection $rows) use ($handle) {
                foreach ($rows as $payment) {
                    fputcsv($handle, [
                        $payment->receipt_number,
                        $payment->appointment?->patient?->name ?? '—',
                        $payment->appointment?->doctor?->name ?? '—',
                        $payment->appointment?->department?->name ?? '—',
                        $payment->paid_at?->format('d M Y g:i a'),
                        $payment->method,
                        number_format((float) $payment->amount, 2),
                        $payment->status,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function metrics(Request $request): array
    {
        [$start, $end] = $this->resolveRange($request);

        $appointments = Appointment::whereBetween('date', [$start->toDateString(), $end->toDateString()])->count();
        $revenue = (float) Payment::where('status', Payment::STATUS_PAID)
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');
        $cancelled = Appointment::where('status', Appointment::STATUS_CANCELLED)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->count();
        $cancellationRate = $appointments > 0 ? round($cancelled / $appointments * 100, 1) : 0;

        $byDepartment = Appointment::query()
            ->selectRaw('department_id, count(*) as total')
            ->with('department:id,name')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('department_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->department->name ?? 'Unknown',
                'count' => $row->total,
            ]);

        $peakHours = Appointment::query()
            ->selectRaw("substr(time_slot, 1, 2) as hour, count(*) as total")
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->hour.':00',
                'count' => $row->total,
            ]);

        $statusBreakdown = Appointment::query()
            ->selectRaw('status, count(*) as total')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $dailyRevenue = $this->dailyRevenue($start, $end);

        $revenueByMethod = Payment::query()
            ->selectRaw('method, sum(amount) as total')
            ->where('status', Payment::STATUS_PAID)
            ->whereBetween('paid_at', [$start, $end])
            ->groupBy('method')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'method' => ucfirst($row->method),
                'total' => (float) $row->total,
            ])
            ->values();

        $refunded = Payment::query()
            ->where('status', Payment::STATUS_REFUNDED)
            ->whereBetween('refunded_at', [$start, $end]);
        $refundCount = (clone $refunded)->count();
        $refundAmount = (float) (clone $refunded)->sum('refund_amount');
        $grossCollected = (float) Payment::whereBetween('paid_at', [$start, $end])
            ->whereIn('status', [Payment::STATUS_PAID, Payment::STATUS_REFUNDED])
            ->sum('amount');
        $refundRate = $grossCollected > 0 ? round($refundAmount / $grossCollected * 100, 1) : 0;

        $doctors = Doctor::query()
            ->with('department:id,name')
            ->withCount(['appointments' => fn ($q) => $q->whereBetween('date', [$start->toDateString(), $end->toDateString()])])
            ->get()
            ->map(function (Doctor $doctor) use ($start, $end) {
                $revenue = Payment::where('status', Payment::STATUS_PAID)
                    ->whereBetween('paid_at', [$start, $end])
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

        $rangeLabel = $this->rangeLabel($request);

        return compact(
            'start', 'end', 'rangeLabel',
            'appointments', 'revenue', 'cancelled', 'cancellationRate',
            'byDepartment', 'peakHours', 'statusBreakdown', 'dailyRevenue',
            'revenueByMethod', 'refundCount', 'refundAmount', 'refundRate', 'doctors',
        );
    }

    private function dailyRevenue(Carbon $start, Carbon $end): Collection
    {
        $days = collect();
        $cursor = $start->copy()->startOfDay();

        while ($cursor->lte($end) && $days->count() < 62) {
            $days->push([
                'label' => $cursor->format('d M'),
                'total' => (float) Payment::where('status', Payment::STATUS_PAID)
                    ->whereDate('paid_at', $cursor->toDateString())
                    ->sum('amount'),
            ]);
            $cursor->addDay();
        }

        return $days;
    }

    private function resolveRange(Request $request): array
    {
        return match ($request->query('range', 'week')) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'custom' => [
                Carbon::parse($request->query('from'))->startOfDay(),
                Carbon::parse($request->query('to'))->endOfDay(),
            ],
            default => [now()->startOfWeek(), now()->endOfWeek()],
        };
    }

    private function rangeLabel(Request $request): string
    {
        return match ($request->query('range', 'week')) {
            'today' => now()->format('l, j F Y'),
            'month' => now()->format('F Y'),
            'custom' => Carbon::parse($request->query('from'))->format('d M Y').' – '.Carbon::parse($request->query('to'))->format('d M Y'),
            default => now()->startOfWeek()->format('d M').' – '.now()->endOfWeek()->format('d M Y'),
        };
    }
}
