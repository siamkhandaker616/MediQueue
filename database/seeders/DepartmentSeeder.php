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
                'name'          => 'General Medicine',
                'slug'          => 'general-medicine',
                'description'   => 'Primary care, fever, infections, diabetes and hypertension management.',
                'icon'          => 'fa-solid fa-stethoscope',
                'room_location' => 'Floor 2, Room 201',
                'floor_number'  => 2,
                'room_number'   => '201',
                'fee_min'       => 500,
                'fee_max'       => 800,
                'fee_range'     => '500 - 800',
            ],
            [
                'name'          => 'Cardiology',
                'slug'          => 'cardiology',
                'description'   => 'Heart conditions, ECG, echocardiogram and blood pressure care.',
                'icon'          => 'fa-solid fa-heart-pulse',
                'room_location' => 'Floor 3, Room 301',
                'floor_number'  => 3,
                'room_number'   => '301',
                'fee_min'       => 1000,
                'fee_max'       => 1500,
                'fee_range'     => '1000 - 1500',
            ],
            [
                'name'          => 'Neurology',
                'slug'          => 'neurology',
                'description'   => 'Disorders of the brain, spine and nervous system.',
                'icon'          => 'fa-solid fa-brain',
                'room_location' => 'Floor 4, Room 401',
                'floor_number'  => 4,
                'room_number'   => '401',
                'fee_min'       => 1000,
                'fee_max'       => 1500,
                'fee_range'     => '1000 - 1500',
            ],
            [
                'name'          => 'Pediatrics',
                'slug'          => 'pediatrics',
                'description'   => 'Health care for infants, children and adolescents.',
                'icon'          => 'fa-solid fa-baby',
                'room_location' => 'Floor 2, Room 205',
                'floor_number'  => 2,
                'room_number'   => '205',
                'fee_min'       => 600,
                'fee_max'       => 900,
                'fee_range'     => '600 - 900',
            ],
            [
                'name'          => 'Dermatology',
                'slug'          => 'dermatology',
                'description'   => 'Skin, hair and nail conditions and treatments.',
                'icon'          => 'fa-solid fa-hand-dots',
                'room_location' => 'Floor 5, Room 501',
                'floor_number'  => 5,
                'room_number'   => '501',
                'fee_min'       => 800,
                'fee_max'       => 1200,
                'fee_range'     => '800 - 1200',
            ],
            [
                'name'          => 'Orthopedics',
                'slug'          => 'orthopedics',
                'description'   => 'Bone, joint, muscle and ligament injuries and care.',
                'icon'          => 'fa-solid fa-bone',
                'room_location' => 'Floor 3, Room 305',
                'floor_number'  => 3,
                'room_number'   => '305',
                'fee_min'       => 800,
                'fee_max'       => 1200,
                'fee_range'     => '800 - 1200',
            ],
            [
                'name'          => 'Gynecology & Obstetrics',
                'slug'          => 'gynecology',
                'description'   => 'Women\'s health, pregnancy care and childbirth.',
                'icon'          => 'fa-solid fa-person-dress',
                'room_location' => 'Floor 2, Room 203',
                'floor_number'  => 2,
                'room_number'   => '203',
                'fee_min'       => 800,
                'fee_max'       => 1200,
                'fee_range'     => '800 - 1200',
            ],
            [
                'name'          => 'ENT',
                'slug'          => 'ent',
                'description'   => 'Ear, nose and throat examinations and treatment.',
                'icon'          => 'fa-solid fa-head-side-cough',
                'room_location' => 'Floor 5, Room 505',
                'floor_number'  => 5,
                'room_number'   => '505',
                'fee_min'       => 600,
                'fee_max'       => 900,
                'fee_range'     => '600 - 900',
            ],
            [
                'name'          => 'Ophthalmology',
                'slug'          => 'ophthalmology',
                'description'   => 'Eye examinations, vision care and minor procedures.',
                'icon'          => 'fa-solid fa-eye',
                'room_location' => 'Floor 6, Room 601',
                'floor_number'  => 6,
                'room_number'   => '601',
                'fee_min'       => 700,
                'fee_max'       => 1000,
                'fee_range'     => '700 - 1000',
            ],
            [
                'name'          => 'Psychiatry',
                'slug'          => 'psychiatry',
                'description'   => 'Mental health evaluation, counseling and therapy.',
                'icon'          => 'fa-solid fa-comments',
                'room_location' => 'Floor 6, Room 605',
                'floor_number'  => 6,
                'room_number'   => '605',
                'fee_min'       => 800,
                'fee_max'       => 1200,
                'fee_range'     => '800 - 1200',
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['slug' => $department['slug']],
                $department + ['is_active' => true]
            );
        }
    }
}