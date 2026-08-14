<?php

namespace Database\Seeders;

use App\Models\PatientMedicalProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@mediqueue.test'],
            [
                'name' => 'MediQueue Admin',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $patients = [
            [
                'name' => 'Demo Patient', 'email' => 'demo@mediqueue.test',
                'profile' => ['blood_type' => 'B+', 'allergies' => ['Penicillin'], 'chronic_conditions' => ['Seasonal asthma']],
            ],
            [
                'name' => 'Ahmed Hossain', 'email' => 'ahmed@mediqueue.test',
                'profile' => ['blood_type' => 'O+', 'allergies' => [], 'chronic_conditions' => ['Hypertension']],
            ],
            [
                'name' => 'Tasnim Rahman', 'email' => 'tasnim@mediqueue.test',
                'profile' => ['blood_type' => 'A+', 'allergies' => ['Dust', 'Peanuts'], 'chronic_conditions' => []],
            ],
            [
                'name' => 'Nadia Karim', 'email' => 'nadia@mediqueue.test',
                'profile' => ['blood_type' => 'AB+', 'allergies' => [], 'chronic_conditions' => ['Diabetes']],
            ],
            [
                'name' => 'Sajid Mahmud', 'email' => 'sajid@mediqueue.test',
                'profile' => ['blood_type' => 'B-', 'allergies' => ['Latex'], 'chronic_conditions' => ['Migraine']],
            ],
        ];

        foreach ($patients as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'password',
                    'role' => 'patient',
                    'email_verified_at' => now(),
                ]
            );

            PatientMedicalProfile::updateOrCreate(
                ['patient_id' => $user->id],
                [
                    'blood_type' => $data['profile']['blood_type'],
                    'allergies' => $data['profile']['allergies'],
                    'chronic_conditions' => $data['profile']['chronic_conditions'],
                    'current_medications' => [],
                    'emergency_contact' => ['name' => 'Family Member', 'phone' => '+880 17xx-xxxxxx', 'relation' => 'Relative'],
                    'additional_notes' => null,
                    'last_updated' => now(),
                ]
            );
        }
    }
}
