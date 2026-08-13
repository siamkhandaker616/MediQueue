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
                'name' => 'Dr. Rafiqul Islam', 'email' => 'rafiqul@mediqueue.test',
                'department' => 'general-medicine', 'qualifications' => 'MBBS, FCPS (Medicine)',
                'specialties' => ['Diabetes', 'Hypertension', 'General consultation'],
                'experience_years' => 15, 'consultation_fee' => 800,
                'languages' => ['Bengali', 'English'], 'bio' => 'Senior consultant physician with 15 years of experience in internal medicine.',
            ],
            [
                'name' => 'Dr. Nusrat Jahan', 'email' => 'nusrat@mediqueue.test',
                'department' => 'cardiology', 'qualifications' => 'MBBS, MD (Cardiology)',
                'specialties' => ['ECG', 'Echocardiography', 'Heart failure'],
                'experience_years' => 12, 'consultation_fee' => 1500,
                'languages' => ['Bengali', 'English'], 'bio' => 'Interventional cardiologist focused on preventive heart care.',
            ],
            [
                'name' => 'Dr. Tanvir Ahmed', 'email' => 'tanvir@mediqueue.test',
                'department' => 'neurology', 'qualifications' => 'MBBS, FCPS (Neurology)',
                'specialties' => ['Migraine', 'Epilepsy', 'Stroke'],
                'experience_years' => 10, 'consultation_fee' => 1500,
                'languages' => ['Bengali', 'English'], 'bio' => 'Neurologist specializing in headache and movement disorders.',
            ],
            [
                'name' => 'Dr. Sadia Chowdhury', 'email' => 'sadia@mediqueue.test',
                'department' => 'pediatrics', 'qualifications' => 'MBBS, DCH',
                'specialties' => ['Child nutrition', 'Vaccination', 'Newborn care'],
                'experience_years' => 8, 'consultation_fee' => 900,
                'languages' => ['Bengali', 'English'], 'bio' => 'Pediatrician passionate about childhood nutrition and vaccination.',
            ],
            [
                'name' => 'Dr. Farzana Haque', 'email' => 'farzana@mediqueue.test',
                'department' => 'dermatology', 'qualifications' => 'MBBS, FCPS (Dermatology)',
                'specialties' => ['Acne', 'Eczema', 'Psoriasis'],
                'experience_years' => 9, 'consultation_fee' => 1200,
                'languages' => ['Bengali', 'English'], 'bio' => 'Dermatologist for medical and cosmetic skin care.',
            ],
            [
                'name' => 'Dr. Mahmudul Hasan', 'email' => 'mahmudul@mediqueue.test',
                'department' => 'orthopedics', 'qualifications' => 'MBBS, MS (Orthopaedics)',
                'specialties' => ['Fracture care', 'Arthritis', 'Sports injury'],
                'experience_years' => 13, 'consultation_fee' => 1200,
                'languages' => ['Bengali', 'English'], 'bio' => 'Orthopedic surgeon experienced in trauma and joint care.',
            ],
            [
                'name' => 'Dr. Sharmin Akter', 'email' => 'sharmin@mediqueue.test',
                'department' => 'gynecology', 'qualifications' => 'MBBS, FCPS (OBGYN)',
                'specialties' => ['Antenatal care', 'Infertility', 'Menstrual health'],
                'experience_years' => 11, 'consultation_fee' => 1200,
                'languages' => ['Bengali', 'English'], 'bio' => 'Obstetrician and gynecologist caring for women at every stage of life.',
            ],
            [
                'name' => 'Dr. Imran Kabir', 'email' => 'imran@mediqueue.test',
                'department' => 'ent', 'qualifications' => 'MBBS, DLO',
                'specialties' => ['Sinusitis', 'Hearing loss', 'Tonsillitis'],
                'experience_years' => 7, 'consultation_fee' => 900,
                'languages' => ['Bengali', 'English'], 'bio' => 'ENT specialist for ear, nose and throat ailments.',
            ],
            [
                'name' => 'Dr. Mehnaz Rahman', 'email' => 'mehnaz@mediqueue.test',
                'department' => 'ophthalmology', 'qualifications' => 'MBBS, MS (Ophthalmology)',
                'specialties' => ['Cataract', 'Glaucoma', 'Vision correction'],
                'experience_years' => 8, 'consultation_fee' => 1000,
                'languages' => ['Bengali', 'English'], 'bio' => 'Ophthalmologist focused on cataract and retinal care.',
            ],
            [
                'name' => 'Dr. Ayesha Siddiqua', 'email' => 'ayesha@mediqueue.test',
                'department' => 'psychiatry', 'qualifications' => 'MBBS, FCPS (Psychiatry)',
                'specialties' => ['Anxiety', 'Depression', 'Stress management'],
                'experience_years' => 6, 'consultation_fee' => 1200,
                'languages' => ['Bengali', 'English'], 'bio' => 'Psychiatrist offering empathetic, evidence-based mental health care.',
            ],
        ];

        foreach ($doctors as $data) {
            $department = Department::where('slug', $data['department'])->firstOrFail();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => 'password',
                'role' => 'doctor',
                'email_verified_at' => now(),
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'qualifications' => $data['qualifications'],
                'specialties' => $data['specialties'],
                'experience_years' => $data['experience_years'],
                'consultation_fee' => $data['consultation_fee'],
                'languages' => $data['languages'],
                'bio' => $data['bio'],
                'is_active' => true,
            ]);
        }
    }
}
