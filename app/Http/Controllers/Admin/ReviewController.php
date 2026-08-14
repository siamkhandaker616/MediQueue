<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::with(['patient', 'doctor'])
            ->latest()
            ->get();

        $pendingCount = $reviews->where('is_visible', false)->count();

        return view('admin.reviews', compact('reviews', 'pendingCount'));
    }

    public function toggle(Review $review): RedirectResponse
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        return back()->with('status', $review->is_visible ? 'Review approved and published.' : 'Review hidden.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'Review removed.');
    }
}
