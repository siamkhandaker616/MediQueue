<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogueTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_catalogue_is_publicly_accessible(): void
    {
        $department = Department::factory()->create();

        $this->get(route('departments.index'))
            ->assertOk()
            ->assertSee($department->name);
    }

    public function test_department_show_page_is_publicly_accessible(): void
    {
        $department = Department::factory()->create();
        $doctor = Doctor::factory()->create(['department_id' => $department->id]);

        $this->get(route('departments.show', $department))
            ->assertOk()
            ->assertSee($department->name)
            ->assertSee($doctor->name);
    }

    public function test_inactive_department_is_not_publicly_visible(): void
    {
        $department = Department::factory()->create(['is_active' => false]);

        $this->get(route('departments.show', $department))->assertNotFound();
    }

    public function test_doctor_directory_is_publicly_accessible(): void
    {
        $doctor = Doctor::factory()->create();

        $this->get(route('doctors.index'))
            ->assertOk()
            ->assertSee($doctor->name);
    }

    public function test_doctor_profile_page_is_publicly_accessible(): void
    {
        $doctor = Doctor::factory()->create();

        $this->get(route('doctors.show', $doctor))
            ->assertOk()
            ->assertSee($doctor->name)
            ->assertSee($doctor->qualifications);
    }

    public function test_inactive_doctor_is_not_publicly_visible(): void
    {
        $doctor = Doctor::factory()->create(['is_active' => false]);

        $this->get(route('doctors.show', $doctor))->assertNotFound();
    }

    public function test_published_reviews_are_shown_on_public_doctor_profile(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['name' => 'Sadia Rahman']);
        Review::factory()->create([
            'doctor_id'    => $doctor->id,
            'patient_id'   => $patient->id,
            'is_visible'   => true,
            'is_anonymous' => false,
            'comment'      => 'Very thorough and attentive.',
        ]);

        $this->get(route('doctors.show', $doctor))
            ->assertOk()
            ->assertSee('Patient reviews')
            ->assertSee('Very thorough and attentive.')
            ->assertSee('Sadia Rahman');
    }

    public function test_hidden_reviews_are_not_shown_publicly_and_anonymity_is_honoured(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = User::factory()->create(['name' => 'Karim Ahmed']);
        Review::factory()->create([
            'doctor_id'    => $doctor->id,
            'patient_id'   => $patient->id,
            'is_visible'   => false,
            'is_anonymous' => false,
            'comment'      => 'Should never appear publicly.',
        ]);
        Review::factory()->create([
            'doctor_id'    => $doctor->id,
            'patient_id'   => $patient->id,
            'is_visible'   => true,
            'is_anonymous' => true,
            'comment'      => 'Kept anonymous on request.',
        ]);

        $this->get(route('doctors.show', $doctor))
            ->assertOk()
            ->assertSee('Kept anonymous on request.')
            ->assertSee('Anonymous patient')
            ->assertDontSee('Should never appear publicly.');
    }
}
