<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'slot_duration' => 'integer',
            'is_active'     => 'boolean',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * FR-03: Real-Time Available Slot Generator
     */
    public static function getSlotsForDoctorAndDate(Doctor $doctor, string $dateString): array
    {
        $carbonDate = Carbon::parse($dateString);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0 = Sunday, 1 = Monday ... 6 = Saturday
        $dayName = strtolower($carbonDate->format('l'));

        // Check if doctor is on approved leave
        if (method_exists($doctor, 'leaves')) {
            $hasLeave = $doctor->leaves()
                ->where('start_date', '<=', $dateString)
                ->where('end_date', '>=', $dateString)
                ->where('status', 'approved')
                ->exists();

            if ($hasLeave) {
                return [
                    'available' => false,
                    'reason'    => 'Dr. ' . ($doctor->display_name ?? $doctor->name) . ' is on approved leave on this date.',
                    'slots'     => [],
                ];
            }
        }

        // 1. Fetch schedules from DB matching day of week
        $schedules = $doctor->schedules()
            ->where('is_active', true)
            ->where(function ($q) use ($dayOfWeek, $dayName) {
                $q->where('day_of_week', $dayOfWeek)
                  ->orWhere('day_of_week', (string) $dayOfWeek)
                  ->orWhere('day_of_week', $dayName);
            })
            ->get();

        // 2. Fallback: If no custom schedule in DB, provide standard hospital shifts for Sun-Thu
        $shiftWindows = [];
        if ($schedules->isNotEmpty()) {
            foreach ($schedules as $s) {
                $shiftWindows[] = [
                    'start'    => $s->start_time,
                    'end'      => $s->end_time,
                    'duration' => $s->slot_duration > 0 ? $s->slot_duration : 30,
                ];
            }
        } elseif ($dayOfWeek !== 5) { // Any day except Friday
            $shiftWindows = [
                ['start' => '09:00', 'end' => '13:00', 'duration' => 30],
                ['start' => '17:00', 'end' => '20:00', 'duration' => 30],
            ];
        } else {
            return [
                'available' => false,
                'reason'    => 'Hospital OPD is closed on Fridays. Please pick Sunday through Thursday.',
                'slots'     => [],
            ];
        }

        // Get currently booked slots to prevent double booking
        $bookedSlots = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', $dateString)
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED])
            ->pluck('time_slot')
            ->toArray();

        // Generate 30-minute slot list
        $slots = [];
        foreach ($shiftWindows as $window) {
            $current = Carbon::parse($dateString . ' ' . $window['start']);
            $end = Carbon::parse($dateString . ' ' . $window['end']);
            $duration = $window['duration'];

            while ($current->copy()->addMinutes($duration)->lte($end)) {
                $startStr = $current->format('H:i');
                $endStr = $current->copy()->addMinutes($duration)->format('H:i');
                $slotLabel = "$startStr - $endStr";

                $isBooked = in_array($slotLabel, $bookedSlots) || in_array($startStr, $bookedSlots);

                $slots[] = [
                    'time'      => $slotLabel,
                    'available' => !$isBooked,
                ];

                $current->addMinutes($duration);
            }
        }

        return [
            'available' => count($slots) > 0,
            'slots'     => $slots,
        ];
    }
}