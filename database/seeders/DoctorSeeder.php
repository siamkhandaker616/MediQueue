<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name'             => 'Dr. Rafiqul Islam',
                'email'            => 'rafiqul@mediqueue.test',
                'department'       => 'general-medicine',
                'specialty'        => 'Senior Consultant Physician',
                'specialties'      => ['Diabetes', 'Hypertension', 'General consultation'],
                'qualifications'   => 'MBBS, FCPS (Medicine)',
                'experience_years' => 15,
                'consultation_fee' => 800,
                'avg_rating'       => 4.8,
                'rating_count'     => 42,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Senior consultant physician with 15 years of experience in internal medicine.',
            ],
            [
                'name'             => 'Dr. Nusrat Jahan',
                'email'            => 'nusrat@mediqueue.test',
                'department'       => 'cardiology',
                'specialty'        => 'Interventional Cardiologist',
                'specialties'      => ['ECG', 'Echocardiography', 'Heart failure'],
                'qualifications'   => 'MBBS, MD (Cardiology)',
                'experience_years' => 12,
                'consultation_fee' => 1500,
                'avg_rating'       => 4.9,
                'rating_count'     => 58,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Interventional cardiologist focused on preventive heart care and echocardiography.',
            ],
            [
                'name'             => 'Dr. Tanvir Ahmed',
                'email'            => 'tanvir@mediqueue.test',
                'department'       => 'neurology',
                'specialty'        => 'Consultant Neurologist',
                'specialties'      => ['Migraine', 'Epilepsy', 'Stroke'],
                'qualifications'   => 'MBBS, FCPS (Neurology)',
                'experience_years' => 10,
                'consultation_fee' => 1500,
                'avg_rating'       => 4.7,
                'rating_count'     => 31,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Neurologist specializing in headache, stroke, and movement disorders.',
            ],
            [
                'name'             => 'Dr. Sadia Chowdhury',
                'email'            => 'sadia@mediqueue.test',
                'department'       => 'pediatrics',
                'specialty'        => 'Consultant Pediatrician',
                'specialties'      => ['Child nutrition', 'Vaccination', 'Newborn care'],
                'qualifications'   => 'MBBS, DCH',
                'experience_years' => 8,
                'consultation_fee' => 900,
                'avg_rating'       => 4.9,
                'rating_count'     => 64,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Pediatrician passionate about childhood nutrition and preventive newborn care.',
            ],
            [
                'name'             => 'Dr. Farzana Haque',
                'email'            => 'farzana@mediqueue.test',
                'department'       => 'dermatology',
                'specialty'        => 'Clinical & Aesthetic Dermatologist',
                'specialties'      => ['Acne', 'Eczema', 'Psoriasis'],
                'qualifications'   => 'MBBS, FCPS (Dermatology)',
                'experience_years' => 9,
                'consultation_fee' => 1200,
                'avg_rating'       => 4.8,
                'rating_count'     => 49,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Dermatologist offering comprehensive medical, laser, and cosmetic skin care.',
            ],
            [
                'name'             => 'Dr. Mahmudul Hasan',
                'email'            => 'mahmudul@mediqueue.test',
                'department'       => 'orthopedics',
                'specialty'        => 'Orthopedic & Trauma Surgeon',
                'specialties'      => ['Fracture care', 'Arthritis', 'Sports injury'],
                'qualifications'   => 'MBBS, MS (Orthopaedics)',
                'experience_years' => 13,
                'consultation_fee' => 1200,
                'avg_rating'       => 4.6,
                'rating_count'     => 28,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Orthopedic surgeon experienced in trauma, arthroscopy, and joint restoration.',
            ],
            [
                'name'             => 'Dr. Sharmin Akter',
                'email'            => 'sharmin@mediqueue.test',
                'department'       => 'gynecology',
                'specialty'        => 'Gynecologist & Obstetrician',
                'specialties'      => ['Antenatal care', 'Infertility', 'Menstrual health'],
                'qualifications'   => 'MBBS, FCPS (OBGYN)',
                'experience_years' => 11,
                'consultation_fee' => 1200,
                'avg_rating'       => 4.9,
                'rating_count'     => 53,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Obstetrician and gynecologist caring for women at every stage of reproductive life.',
            ],
            [
                'name'             => 'Dr. Imran Kabir',
                'email'            => 'imran@mediqueue.test',
                'department'       => 'ent',
                'specialty'        => 'ENT Specialist & Surgeon',
                'specialties'      => ['Sinusitis', 'Hearing loss', 'Tonsillitis'],
                'qualifications'   => 'MBBS, DLO',
                'experience_years' => 7,
                'consultation_fee' => 900,
                'avg_rating'       => 4.5,
                'rating_count'     => 22,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'ENT specialist treating chronic sinusitis, hearing disorders, and throat ailments.',
            ],
            [
                'name'             => 'Dr. Mehnaz Rahman',
                'email'            => 'mehnaz@mediqueue.test',
                'department'       => 'ophthalmology',
                'specialty'        => 'Ophthalmologist & Eye Surgeon',
                'specialties'      => ['Cataract', 'Glaucoma', 'Vision correction'],
                'qualifications'   => 'MBBS, MS (Ophthalmology)',
                'experience_years' => 8,
                'consultation_fee' => 1000,
                'avg_rating'       => 4.7,
                'rating_count'     => 35,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Ophthalmologist focused on modern cataract surgery, glaucoma, and vision care.',
            ],
            [
                'name'             => 'Dr. Ayesha Siddiqua',
                'email'            => 'ayesha@mediqueue.test',
                'department'       => 'psychiatry',
                'specialty'        => 'Psychiatrist & Psychotherapist',
                'specialties'      => ['Anxiety', 'Depression', 'Stress management'],
                'qualifications'   => 'MBBS, FCPS (Psychiatry)',
                'experience_years' => 6,
                'consultation_fee' => 1200,
                'avg_rating'       => 4.8,
                'rating_count'     => 39,
                'languages'        => ['Bengali', 'English'],
                'bio'              => 'Psychiatrist offering empathetic, evidence-based clinical and psychological care.',
            ],
        ];

        foreach ($doctors as $data) {
            $department = Department::where('slug', $data['department'])->firstOrFail();

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => bcrypt('password'),
                    'role'              => 'doctor',
                    'email_verified_at' => now(),
                ]
            );

            Doctor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id'    => $department->id,
                    'name'             => $data['name'],
                    'email'            => $data['email'],
                    'slug'             => Str::slug($data['name']),
                    'specialty'        => $data['specialty'],
                    'specialties'      => $data['specialties'],
                    'qualifications'   => $data['qualifications'],
                    'experience_years' => $data['experience_years'],
                    'consultation_fee' => $data['consultation_fee'],
                    'avg_rating'       => $data['avg_rating'],
                    'rating_count'     => $data['rating_count'],
                    'languages'        => $data['languages'],
                    'bio'              => $data['bio'],
                    'is_active'        => true,
                ]
            );
        }
    }
}