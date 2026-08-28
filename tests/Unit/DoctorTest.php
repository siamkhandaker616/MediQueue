<?php

namespace Tests\Unit;

use App\Models\Doctor;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorTest extends TestCase
{
    use RefreshDatabase;

    public function test_rating_averages_only_visible_reviews(): void
    {
        $doctor = Doctor::factory()->create();

        Review::factory()->create(['doctor_id' => $doctor->id, 'overall_rating' => 4, 'is_visible' => true]);
        Review::factory()->create(['doctor_id' => $doctor->id, 'overall_rating' => 2, 'is_visible' => true]);
        Review::factory()->create(['doctor_id' => $doctor->id, 'overall_rating' => 5, 'is_visible' => false]);

        $this->assertSame(3.0, $doctor->rating_avg);
        $this->assertSame(2, $doctor->rating_count);
    }

    public function test_moderation_recomputes_stored_rating_columns(): void
    {
        $doctor = Doctor::factory()->create(['avg_rating' => 0, 'rating_count' => 0]);

        $visible = Review::factory()->create(['doctor_id' => $doctor->id, 'overall_rating' => 5, 'is_visible' => true]);
        Review::factory()->create(['doctor_id' => $doctor->id, 'overall_rating' => 4, 'is_visible' => true]);

        $doctor->refresh();
        $this->assertEqualsWithDelta(4.5, (float) $doctor->avg_rating, 0.01);
        $this->assertSame(2, $doctor->rating_count);

        $visible->update(['is_visible' => false]);
        $doctor->refresh();
        $this->assertEqualsWithDelta(4.0, (float) $doctor->avg_rating, 0.01);
        $this->assertSame(1, $doctor->rating_count);

        $visible->delete();
        $doctor->refresh();
        $this->assertEqualsWithDelta(4.0, (float) $doctor->avg_rating, 0.01);
        $this->assertSame(1, $doctor->rating_count);
    }
}
