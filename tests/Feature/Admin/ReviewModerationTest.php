<?php

namespace Tests\Feature\Admin;

use App\Models\Doctor;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_pending_reviews(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $review = Review::factory()->create(['is_visible' => false]);

        $response = $this->actingAs($admin)->get('/admin/reviews');

        $response->assertOk();
        $response->assertSee($review->patient->name);
        $response->assertSee($review->comment);
    }

    public function test_admin_can_approve_a_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $review = Review::factory()->create(['is_visible' => false]);

        $this->actingAs($admin)
            ->patch("/admin/reviews/{$review->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_visible' => true]);
    }

    public function test_admin_can_hide_a_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $review = Review::factory()->create(['is_visible' => true]);

        $this->actingAs($admin)
            ->patch("/admin/reviews/{$review->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_visible' => false]);
    }

    public function test_admin_can_delete_a_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $review = Review::factory()->create();

        $this->actingAs($admin)->delete("/admin/reviews/{$review->id}")->assertRedirect();

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_doctor_cannot_delete_a_review(): void
    {
        $doctor = Doctor::factory()->create();
        $review = Review::factory()->create();

        $this->actingAs($doctor->user)->delete("/admin/reviews/{$review->id}")->assertForbidden();
    }

    public function test_doctor_cannot_access_review_moderation(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor->user)->get('/admin/reviews')->assertForbidden();
    }
}
