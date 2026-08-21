<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Notification;

class QueuePositionAlert extends Notification
{
    public const KIND_READY = 'ready';

    public const KIND_APPROACHING = 'approaching';

    public function __construct(
        private readonly Appointment $appointment,
        private readonly string $kind,
        private readonly int $patientsAhead,
    ) {}

    /**
     * FR-18: Queue position alerts delivered to the patient's on-site feed.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'kind' => $this->kind,
            'patients_ahead' => $this->patientsAhead,
            'doctor_name' => $this->appointment->doctor->name,
            'department_name' => $this->appointment->department->name,
            'token_number' => $this->appointment->token_number,
            'date' => $this->appointment->date->toDateString(),
            'time_slot' => $this->appointment->time_slot,
        ];
    }
}
