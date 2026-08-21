<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MediQueue');
    }

    public function test_landing_lists_departments_with_doctor_counts(): void
    {
        $department = Department::factory()->create(['name' => 'General Medicine']);
        Doctor::factory()->count(3)->create(['department_id' => $department->id]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('General Medicine');
        $response->assertSee('3 doctors');
        $response->assertSee(route('departments.show', $department));
    }

    public function test_landing_lists_top_rated_doctors(): void
    {
        $doctor = Doctor::factory()->create([
            'name' => 'Dr. Nusrat Jahan',
            'specialty' => 'Interventional Cardiologist',
            'avg_rating' => 4.9,
            'rating_count' => 58,
            'consultation_fee' => 1500,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Dr. Nusrat Jahan');
        $response->assertSee('Interventional Cardiologist');
        $response->assertSee('4.9');
        $response->assertSee(route('doctors.show', $doctor));
    }

    public function test_landing_links_to_catalogue_and_directory(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('departments.index'));
        $response->assertSee(route('doctors.index'));
    }

    public function test_guest_sees_login_and_register_actions(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('login'));
        $response->assertSee(route('register'));
    }

    public function test_authenticated_user_sees_dashboard_actions(): void
    {
        $user = User::factory()->create(['role' => 'patient', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee(route('dashboard'));
        $response->assertDontSee(route('register'));
    }
}
