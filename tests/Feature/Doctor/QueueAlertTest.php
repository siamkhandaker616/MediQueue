<?php

namespace Tests\Feature\Doctor;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Notifications\QueuePositionAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_waiting_patients_receive_alerts_when_queue_advances(): void
    {
        $doctor = Doctor::factory()->create();
        $appointments = $this->makeQueue($doctor, 4);

        $this->actingAs($doctor->user)
            ->patch("/doctor/queue/{$appointments[0]->id}/status", ['status' => Appointment::STATUS_IN_PROGRESS])
            ->assertRedirect();

        $this->assertTrue($this->hasAlert($appointments[1]->patient, $appointments[1]->id, QueuePositionAlert::KIND_READY));
        $this->assertTrue($this->hasAlert($appointments[2]->patient, $appointments[2]->id, QueuePositionAlert::KIND_APPROACHING));
        $this->assertTrue($this->hasAlert($appointments[3]->patient, $appointments[3]->id, QueuePositionAlert::KIND_APPROACHING));

        $this->assertFalse($this->hasAlert($appointments[0]->patient, $appointments[0]->id, QueuePositionAlert::KIND_READY));
    }

    public function test_alerts_are_sent_once_per_stage(): void
    {
        $doctor = Doctor::factory()->create();
        $appointments = $this->makeQueue($doctor, 2);

        foreach ([1, 2] as $_) {
            $this->actingAs($doctor->user)
                ->patch("/doctor/queue/{$appointments[0]->id}/status", ['status' => Appointment::STATUS_IN_PROGRESS]);
        }

        $totalAlerts = $appointments[1]->patient->notifications()
            ->where('type', QueuePositionAlert::class)
            ->count();

        $this->assertSame(1, $totalAlerts);
    }

    public function test_ready_alert_upgrades_when_patient_reaches_front(): void
    {
        $doctor = Doctor::factory()->create();
        $appointments = $this->makeQueue($doctor, 3);

        $this->actingAs($doctor->user)
            ->patch("/doctor/queue/{$appointments[0]->id}/status", ['status' => Appointment::STATUS_COMPLETED]);

        $this->assertTrue($this->hasAlert($appointments[1]->patient, $appointments[1]->id, QueuePositionAlert::KIND_READY));
        $this->assertTrue($this->hasAlert($appointments[2]->patient, $appointments[2]->id, QueuePositionAlert::KIND_APPROACHING));
        $this->assertFalse($this->hasAlert($appointments[2]->patient, $appointments[2]->id, QueuePositionAlert::KIND_READY));

        $this->actingAs($doctor->user)
            ->patch("/doctor/queue/{$appointments[1]->id}/status", ['status' => Appointment::STATUS_IN_PROGRESS]);

        $this->assertTrue($this->hasAlert($appointments[2]->patient, $appointments[2]->id, QueuePositionAlert::KIND_READY));
    }

    public function test_patients_of_other_doctors_are_not_alerted(): void
    {
        $doctor = Doctor::factory()->create();
        $other = Doctor::factory()->create();

        $mine = $this->makeQueue($doctor, 1);
        $theirs = $this->makeQueue($other, 1);

        $this->actingAs($doctor->user)
            ->patch("/doctor/queue/{$mine[0]->id}/status", ['status' => Appointment::STATUS_COMPLETED]);

        $this->assertSame(0, $theirs[0]->patient->notifications()
            ->where('type', QueuePositionAlert::class)
            ->count());
    }

    private function makeQueue(Doctor $doctor, int $count): array
    {
        $slots = ['09:00', '10:00', '11:00', '12:00'];

        $appointments = [];
        foreach (range(0, $count - 1) as $i) {
            $appointments[] = Appointment::factory()->create([
                'doctor_id' => $doctor->id,
                'department_id' => $doctor->department_id,
                'date' => now()->today(),
                'time_slot' => $slots[$i],
                'status' => Appointment::STATUS_CHECKED_IN,
            ]);
        }

        return $appointments;
    }

    private function hasAlert(User $patient, int $appointmentId, string $kind): bool
    {
        return $patient->notifications()
            ->where('type', QueuePositionAlert::class)
            ->where('data->appointment_id', $appointmentId)
            ->where('data->kind', $kind)
            ->exists();
    }
}
