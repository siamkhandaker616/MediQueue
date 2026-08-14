<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DoctorAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_create_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/doctors/create');

        $response->assertOk();
        $response->assertSee('Add doctor');
    }

    public function test_admin_can_create_a_doctor_with_user_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $department = Department::factory()->create();

        $this->actingAs($admin)->post('/admin/doctors', [
            'name' => 'Dr. Alim Uddin',
            'email' => 'alim@example.com',
            'department_id' => $department->id,
            'qualifications' => 'MBBS, FCPS',
            'specialties' => 'Cardiology, Interventional',
            'consultation_fee' => 800,
        ])->assertRedirect(route('admin.doctors.index'));

        $this->assertDatabaseHas('users', ['email' => 'alim@example.com', 'role' => 'doctor']);
        $this->assertDatabaseHas('doctors', [
            'email' => 'alim@example.com',
            'name' => 'Dr. Alim Uddin',
            'department_id' => $department->id,
            'consultation_fee' => 800.00,
        ]);

        $doctor = Doctor::where('email', 'alim@example.com')->firstOrFail();
        $this->assertSame($doctor->id, $doctor->user->doctor->id);
        $this->assertSame(['Cardiology', 'Interventional'], $doctor->specialties);
    }

    public function test_creating_a_doctor_requires_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/admin/doctors', [
            'name' => '',
            'email' => 'not-an-email',
            'consultation_fee' => '',
        ])->assertSessionHasErrors(['name', 'email', 'department_id', 'qualifications', 'consultation_fee']);

        $this->assertDatabaseCount('doctors', 0);
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/doctors/{$doctor->id}/edit");

        $response->assertOk();
        $response->assertSee($doctor->name);
    }

    public function test_admin_can_update_a_doctor_and_reset_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $department = Department::factory()->create();
        $doctor = Doctor::factory()->create(['department_id' => $department->id]);

        $this->actingAs($admin)->patch("/admin/doctors/{$doctor->id}", [
            'name' => 'Dr. Alim Uddin (Updated)',
            'email' => $doctor->email,
            'password' => 'newsecret123',
            'department_id' => $department->id,
            'qualifications' => 'MBBS, MD',
            'consultation_fee' => 1000,
        ])->assertRedirect();

        $doctor->refresh();

        $this->assertSame('Dr. Alim Uddin (Updated)', $doctor->name);
        $this->assertSame('Dr. Alim Uddin (Updated)', $doctor->user->name);
        $this->assertSame(1000.0, (float) $doctor->consultation_fee);
        $this->assertTrue(Hash::check('newsecret123', $doctor->user->password));
    }

    public function test_admin_can_toggle_doctor_active_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = Doctor::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->patch("/admin/doctors/{$doctor->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('doctors', ['id' => $doctor->id, 'is_active' => false]);
    }

    public function test_doctor_cannot_access_doctor_management(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor->user)->get('/admin/doctors/create')->assertForbidden();
        $this->actingAs($doctor->user)->post('/admin/doctors')->assertForbidden();
    }
}
