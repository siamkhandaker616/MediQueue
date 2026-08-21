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
        'slot_duration_minutes',
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

    /* -------------------------------------------------------------------------- */
    /*                    Real-Time Slot Generator (FR-03)                        */
    /* -------------------------------------------------------------------------- */

    /**
     * Generates all available slots across all shifts (morning & evening) on a date.
     */
    public static function getSlotsForDoctorAndDate(Doctor $doctor, string $dateString): array
    {
        $date = Carbon::parse($dateString);
        $dayName = $date->format('l');      // "Sunday", "Monday", etc.
        $dayNumber = $date->dayOfWeek;      // 0 (Sunday) to 6 (Saturday)

        // 1. Check if doctor is on approved leave
        if (DoctorLeave::where('doctor_id', $doctor->id)->whereDate('date', $dateString)->exists()) {
            return [
                'available' => false,
                'reason'    => 'Doctor is on approved leave on this date.',
                'slots'     => [],
            ];
        }

        // 2. Fetch all shifts for this day (handles multiple shifts per day)
        $schedules = self::where('doctor_id', $doctor->id)
            ->where(function ($q) use ($dayName, $dayNumber) {
                $q->where('day_of_week', $dayName)
                  ->orWhere('day_of_week', (string) $dayNumber);
            })
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return [
                'available' => false,
                'reason'    => "Doctor does not practice on {$dayName}s.",
                'slots'     => [],
            ];
        }

        // 3. Fetch already booked slots for this doctor on this date
        $bookedSlots = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', $dateString)
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED])
            ->pluck('time_slot')
            ->toArray();

        // 4. Generate slots for each shift
        $slots = [];

        foreach ($schedules as $schedule) {
            $duration = $schedule->slot_duration ?: 30;
            $startTime = Carbon::parse($dateString . ' ' . $schedule->start_time);
            $endTime = Carbon::parse($dateString . ' ' . $schedule->end_time);

            while ($startTime->copy()->addMinutes($duration)->lte($endTime)) {
                $slotStart = $startTime->format('H:i');
                $slotEnd = $startTime->copy()->addMinutes($duration)->format('H:i');
                $slotLabel = "{$slotStart} - {$slotEnd}";

                $isBooked = in_array($slotLabel, $bookedSlots);

                $slots[] = [
                    'time'      => $slotLabel,
                    'available' => !$isBooked,
                ];

                $startTime->addMinutes($duration);
            }
        }

        return [
            'available' => true,
            'reason'    => null,
            'slots'     => $slots,
        ];
    }
}