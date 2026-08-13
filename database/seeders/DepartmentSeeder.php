<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'General Medicine', 'slug' => 'general-medicine',
                'description' => 'Primary care, fever, infections, diabetes and hypertension management.',
                'fee_range' => '500 - 800', 'floor_number' => 2, 'room_number' => '201',
            ],
            [
                'name' => 'Cardiology', 'slug' => 'cardiology',
                'description' => 'Heart conditions, ECG, echocardiogram and blood pressure care.',
                'fee_range' => '1000 - 1500', 'floor_number' => 3, 'room_number' => '301',
            ],
            [
                'name' => 'Neurology', 'slug' => 'neurology',
                'description' => 'Disorders of the brain, spine and nervous system.',
                'fee_range' => '1000 - 1500', 'floor_number' => 4, 'room_number' => '401',
            ],
            [
                'name' => 'Pediatrics', 'slug' => 'pediatrics',
                'description' => 'Health care for infants, children and adolescents.',
                'fee_range' => '600 - 900', 'floor_number' => 2, 'room_number' => '205',
            ],
            [
                'name' => 'Dermatology', 'slug' => 'dermatology',
                'description' => 'Skin, hair and nail conditions and treatments.',
                'fee_range' => '800 - 1200', 'floor_number' => 5, 'room_number' => '501',
            ],
            [
                'name' => 'Orthopedics', 'slug' => 'orthopedics',
                'description' => 'Bone, joint, muscle and ligament injuries and care.',
                'fee_range' => '800 - 1200', 'floor_number' => 3, 'room_number' => '305',
            ],
            [
                'name' => 'Gynecology & Obstetrics', 'slug' => 'gynecology',
                'description' => 'Women\'s health, pregnancy care and childbirth.',
                'fee_range' => '800 - 1200', 'floor_number' => 2, 'room_number' => '203',
            ],
            [
                'name' => 'ENT', 'slug' => 'ent',
                'description' => 'Ear, nose and throat examinations and treatment.',
                'fee_range' => '600 - 900', 'floor_number' => 5, 'room_number' => '505',
            ],
            [
                'name' => 'Ophthalmology', 'slug' => 'ophthalmology',
                'description' => 'Eye examinations, vision care and minor procedures.',
                'fee_range' => '700 - 1000', 'floor_number' => 6, 'room_number' => '601',
            ],
            [
                'name' => 'Psychiatry', 'slug' => 'psychiatry',
                'description' => 'Mental health evaluation, counseling and therapy.',
                'fee_range' => '800 - 1200', 'floor_number' => 6, 'room_number' => '605',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department + ['is_active' => true]);
        }
    }
}
