<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_delete_departments(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/admin/departments', [
            'name' => 'Nephrology',
            'fee_range' => '900 - 1200',
            'description' => 'Kidney care',
        ])->assertRedirect();

        $this->assertDatabaseHas('departments', ['name' => 'Nephrology']);

        $department = Department::where('name', 'Nephrology')->firstOrFail();

        $this->actingAs($admin)
            ->delete("/admin/departments/{$department->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_department_with_doctors_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = Doctor::factory()->create();
        $department = $doctor->department;

        $this->actingAs($admin)
            ->delete("/admin/departments/{$department->id}")
            ->assertStatus(422);

        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }
}
