<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Doctor;
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
}
