<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MedicationLog;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MedicationTrackerController extends Controller
{
    public function index()
    {
        $patientId = auth()->id();
        $today = Carbon::today();

        $prescriptions = Prescription::where('patient_id', $patientId)
            ->where('created_at', '>=', now()->subDays(30))
            ->with(['items', 'doctor.user', 'doctor.department'])
            ->orderByDesc('created_at')
            ->get();

        $items = $prescriptions->flatMap(function ($rx) {
            return $rx->items->map(function ($item) use ($rx) {
                $item->prescription_number = $rx->prescription_number;
                $item->doctor_name = $rx->doctor->display_name ?? $rx->doctor->user->name;
                $item->department_name = $rx->doctor->department->name ?? 'General';
                return $item;
            });
        });

        $todayLogs = MedicationLog::where('user_id', $patientId)
            ->where('scheduled_date', $today)
            ->get()
            ->keyBy(fn ($l) => $l->prescription_item_id . '_' . $l->slot);

        return view('patient.medication-tracker.index', compact('items', 'todayLogs', 'today'));
    }

    public function log(Request $request)
    {
        $validated = $request->validate([
            'prescription_item_id' => 'required|exists:prescription_items,id',
            'slot'                 => 'required|in:morning,afternoon,evening',
            'status'               => 'required|in:taken,skipped,missed',
            'scheduled_date'       => 'required|date|before_or_equal:today',
        ]);

        $patientId = auth()->id();

        $item = PrescriptionItem::findOrFail($validated['prescription_item_id']);

        if ($item->prescription->patient_id !== $patientId) {
            abort(403);
        }

        $existing = MedicationLog::where([
            'user_id'             => $patientId,
            'prescription_item_id' => $validated['prescription_item_id'],
            'scheduled_date'      => $validated['scheduled_date'],
            'slot'                => $validated['slot'],
        ])->first();

        if ($existing) {
            if ($existing->status === $validated['status']) {
                $existing->delete();
                return response()->json(['status' => null, 'message' => 'Log removed']);
            }
            $existing->update([
                'status'   => $validated['status'],
                'logged_at' => now(),
            ]);
            return response()->json(['status' => $validated['status'], 'message' => 'Updated']);
        }

        MedicationLog::create([
            'user_id'              => $patientId,
            'prescription_item_id' => $validated['prescription_item_id'],
            'prescription_id'      => $item->prescription_id,
            'scheduled_date'       => $validated['scheduled_date'],
            'slot'                 => $validated['slot'],
            'status'               => $validated['status'],
            'logged_at'            => now(),
        ]);

        return response()->json(['status' => $validated['status'], 'message' => 'Logged']);
    }

    public function history()
    {
        $patientId = auth()->id();
        $end = Carbon::today();
        $start = $end->copy()->subDays(29);

        $prescriptions = Prescription::where('patient_id', $patientId)
            ->where('created_at', '>=', $start)
            ->with('items')
            ->get();

        $allItemIds = $prescriptions->flatMap(fn ($rx) => $rx->items->pluck('id'))->unique();

        $logs = MedicationLog::where('user_id', $patientId)
            ->whereBetween('scheduled_date', [$start, $end])
            ->whereIn('prescription_item_id', $allItemIds)
            ->get();

        $days = collect();
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dateStr = $d->toDateString();
            $dayLogs = $logs->where('scheduled_date', $dateStr);
            $totalSlots = $prescriptions->sum(function ($rx) {
                return $rx->items->count() * 3;
            });
            $taken = $dayLogs->where('status', 'taken')->count();
            $skipped = $dayLogs->where('status', 'skipped')->count();
            $logged = $taken + $skipped;

            $days->push([
                'date'      => $d->copy(),
                'taken'     => $taken,
                'skipped'   => $skipped,
                'logged'    => $logged,
                'total'     => $totalSlots,
                'pct'       => $totalSlots > 0 ? round(($taken / $totalSlots) * 100) : 0,
            ]);
        }

        $totalTaken = $logs->where('status', 'taken')->count();
        $totalLogged = $logs->whereIn('status', ['taken', 'skipped'])->count();
        $overallPct = $totalLogged > 0 ? round(($totalTaken / $totalLogged) * 100) : 0;

        return view('patient.medication-tracker.history', compact('days', 'overallPct', 'start', 'end'));
    }
}
