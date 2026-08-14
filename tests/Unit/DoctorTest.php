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
}
