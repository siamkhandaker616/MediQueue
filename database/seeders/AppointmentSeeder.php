<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    private const SLOT_START = 9;

    private const SLOT_END = 12;

    private const STATUSES = [
        Appointment::STATUS_SCHEDULED,
        Appointment::STATUS_CHECKED_IN,
        Appointment::STATUS_IN_PROGRESS,
        Appointment::STATUS_COMPLETED,
        Appointment::STATUS_CANCELLED,
    ];

    private array $usedSlots = [];

    public function run(): void
    {
        if (Appointment::exists()) {
            return;
        }

        $patients = User::where('role', 'patient')->get();

        foreach (Doctor::with('department')->get() as $doctor) {
            $this->seedPastAppointments($doctor, $patients);
            $this->seedTodayQueue($doctor, $patients);
            $this->seedTomorrowAppointments($doctor, $patients);
        }
    }

    private function seedPastAppointments(Doctor $doctor, $patients): void
    {
        foreach (range(7, 1) as $daysAgo) {
            $date = now()->subDays($daysAgo)->startOfDay();

            foreach (range(1, rand(1, 3)) as $_) {
                $patient = $patients->random();
                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'department_id' => $doctor->department_id,
                    'date' => $date,
                    'time_slot' => $this->nextSlot(),
                    'status' => Appointment::STATUS_COMPLETED,
                    'notes' => null,
                ]);

                $this->createPayment($appointment, $date);

                $prescription = Prescription::create([
                    'appointment_id' => $appointment->id,
                    'doctor_id' => $doctor->id,
                    'patient_id' => $patient->id,
                    'diagnosis' => fake()->randomElement([
                        'Viral fever with upper respiratory infection',
                        'Mild hypertension — regular monitoring advised',
                        'Gastritis, likely stress related',
                        'Seasonal allergies with mild rhinitis',
                        'Vitamin D deficiency',
                        'Mild migraine without aura',
                    ]),
                    'investigation' => fake()->randomElement([
                        'Complete blood count, ESR',
                        'Blood pressure diary for two weeks',
                        'Lipid profile, fasting blood sugar',
                        'Thyroid function test',
                        null,
                    ]),
                    'follow_up_date' => fake()->randomElement([$date->copy()->addDays(14), null]),
                    'dietary_advice' => fake()->randomElement([
                        'Low salt, low fat diet. Avoid fried foods.',
                        'Increase water intake; avoid spicy food.',
                        'Balanced meals at regular intervals.',
                        null,
                    ]),
                    'doctor_notes' => fake()->randomElement(['Take rest and fluids. Call if fever persists.', null]),
                    'is_editable' => false,
                ]);

                foreach ($this->randomMedicationRows() as $row) {
                    $prescription->items()->create($row);
                }

                if (rand(1, 100) <= 45) {
                    $this->createReview($appointment);
                }
            }

            $this->usedSlots = [];
        }
    }

    private function seedTodayQueue(Doctor $doctor, $patients): void
    {
        $queue = [
            Appointment::STATUS_COMPLETED,
            Appointment::STATUS_IN_PROGRESS,
            Appointment::STATUS_CHECKED_IN,
            Appointment::STATUS_CHECKED_IN,
            Appointment::STATUS_SCHEDULED,
            Appointment::STATUS_SCHEDULED,
        ];

        foreach ($queue as $status) {
            $patient = $patients->random();
            $date = now()->startOfDay();

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'department_id' => $doctor->department_id,
                'date' => $date,
                'time_slot' => $this->nextSlot(),
                'status' => $status,
                'notes' => $status === Appointment::STATUS_SCHEDULED ? null : null,
            ]);

            if ($status === Appointment::STATUS_COMPLETED) {
                $this->createPayment($appointment, $date);
            }
        }

        $this->usedSlots = [];
    }

    private function seedTomorrowAppointments(Doctor $doctor, $patients): void
    {
        foreach (range(1, rand(2, 3)) as $_) {
            $patient = $patients->random();
            $date = now()->addDay()->startOfDay();

            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'department_id' => $doctor->department_id,
                'date' => $date,
                'time_slot' => $this->nextSlot(),
                'status' => Appointment::STATUS_SCHEDULED,
                'notes' => null,
            ]);
        }

        $this->usedSlots = [];
    }

    private function createPayment(Appointment $appointment, $date): void
    {
        Payment::create([
            'appointment_id' => $appointment->id,
            'amount' => $appointment->doctor->consultation_fee,
            'method' => fake()->randomElement(['bkash', 'nagad', 'card']),
            'transaction_id' => strtoupper(fake()->bothify('TXN####??##')),
            'gateway_response' => ['sandbox' => true],
            'status' => Payment::STATUS_PAID,
            'paid_at' => $date->copy()->addMinutes(rand(10, 300)),
        ]);
    }

    private function createReview(Appointment $appointment): void
    {
        Review::create([
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'appointment_id' => $appointment->id,
            'punctuality_rating' => rand(3, 5),
            'communication_rating' => rand(3, 5),
            'knowledge_rating' => rand(3, 5),
            'overall_rating' => rand(3, 5),
            'comment' => fake()->randomElement([
                'Very professional and attentive doctor.',
                'Listened carefully and explained everything clearly.',
                'Short wait time and friendly staff.',
                'Prescription was very helpful, felt better in two days.',
                null,
            ]),
            'is_visible' => rand(1, 100) <= 60,
        ]);
    }

    private function randomMedicationRows(): array
    {
        $meds = [
            ['Paracetamol 500mg', '500 mg', '1 tablet every 6 hours after meals', '5 days'],
            ['Amoxicillin 500mg', '500 mg', '1 capsule every 8 hours', '7 days'],
            ['Omeprazole 20mg', '20 mg', '1 capsule before breakfast', '14 days'],
            ['Salbutamol inhaler', '100 mcg', '2 puffs as needed', '7 days'],
            ['Cetirizine 10mg', '10 mg', '1 tablet at night', '10 days'],
            ['Losartan 50mg', '50 mg', '1 tablet every morning', '30 days'],
            ['Vitamin D3 1000IU', '1000 IU', '1 softgel after dinner', '30 days'],
            ['Diclofenac gel', 'topical', 'Apply twice daily on affected area', '5 days'],
        ];

        $rows = [];
        foreach (fake()->randomElements($meds, rand(1, 3)) as [$name, $dosage, $frequency, $duration]) {
            $rows[] = [
                'medication_name' => $name,
                'dosage' => $dosage,
                'frequency' => $frequency,
                'duration' => $duration,
                'instructions' => fake()->randomElement(['Take with food', 'Drink plenty of water', 'Avoid during pregnancy', null]),
            ];
        }

        return $rows;
    }

    private function nextSlot(): string
    {
        do {
            $minute = rand(0, 1) * 30;
            $hour = rand(self::SLOT_START, self::SLOT_END - 1);
            $slot = sprintf('%02d:%02d', $hour, $minute);
        } while (in_array($slot, $this->usedSlots, true));

        $this->usedSlots[] = $slot;

        return $slot;
    }
}
