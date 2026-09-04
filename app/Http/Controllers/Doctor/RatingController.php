<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\View\View;

class RatingController extends Controller
{
    public function index(): View
    {
        $doctor = auth()->user()->doctor;

        $reviews = $doctor->reviews()
            ->with('patient')
            ->where('is_visible', true)
            ->latest()
            ->get();

        $summary = [
            'overall'       => (float) round($reviews->avg('overall_rating') ?? 0.0, 1),
            'punctuality'   => (float) round($reviews->avg('punctuality_rating') ?? 0.0, 1),
            'communication' => (float) round($reviews->avg('communication_rating') ?? 0.0, 1),
            'knowledge'     => (float) round($reviews->avg('knowledge_rating') ?? 0.0, 1),
            'count'         => $reviews->count(),
        ];

        return view('doctor.ratings', compact('reviews', 'summary'));
    }
}
