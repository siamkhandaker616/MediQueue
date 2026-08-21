<?php

namespace App\Services;

use App\Models\Appointment;
use App\Notifications\QueuePositionAlert;

/**
 * FR-18: Queue Position Alerts.
 *
 * Recomputes the live queue for a doctor's day after any status change and
 * notifies waiting patients whose turn is ready or approaching (2 ahead).
 * Each alert stage is sent at most once per appointment.
 */
class QueueAlertService
{
    public function dispatchFor(Appointment $appointment): void
    {
        $waiting = Appointment::query()
            ->with(['patient', 'doctor', 'department'])
            ->where('doctor_id', $appointment->doctor_id)
            ->whereDate('date', $appointment->date)
            ->whereIn('status', [Appointment::STATUS_SCHEDULED, Appointment::STATUS_CHECKED_IN])
            ->orderBy('time_slot')
            ->orderBy('id')
            ->get();

        foreach ($waiting as $index => $waitingAppointment) {
            $kind = match (true) {
                $index === 0 => QueuePositionAlert::KIND_READY,
                $index <= 2 => QueuePositionAlert::KIND_APPROACHING,
                default => null,
            };

            if ($kind === null || $waitingAppointment->patient === null) {
                continue;
            }

            if ($this->alreadySent($waitingAppointment, $kind)) {
                continue;
            }

            $waitingAppointment->patient->notify(new QueuePositionAlert(
                $waitingAppointment,
                $kind,
                $index,
            ));
        }
    }

    private function alreadySent(Appointment $appointment, string $kind): bool
    {
        return $appointment->patient->notifications()
            ->where('type', QueuePositionAlert::class)
            ->where('data->appointment_id', $appointment->id)
            ->where('data->kind', $kind)
            ->exists();
    }
}
