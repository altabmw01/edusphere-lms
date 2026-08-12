<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('moderate', Review::class);

        $reviews = Review::query()
            ->with(['user', 'reviewable'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20);

        return view('manager.reviews.index', ['reviews' => $reviews]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $this->authorize('moderate', Review::class);

        $review->update(['status' => 'approved']);
        $this->recalculateRating($review);

        return back()->with('status', 'Review approved.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $this->authorize('moderate', Review::class);

        $review->update(['status' => 'rejected']);
        $this->recalculateRating($review);

        return back()->with('status', 'Review rejected.');
    }

    public function reply(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('reply', $review);

        $request->validate(['reply' => ['required', 'string', 'max:2000']]);

        $review->update(['reply' => $request->string('reply'), 'replied_at' => now()]);

        return back()->with('status', 'Reply posted.');
    }

    protected function recalculateRating(Review $review): void
    {
        $model = $review->reviewable;

        if (! $model) {
            return;
        }

        $approved = $model->approvedReviews();
        $model->update([
            'rating_avg' => round($approved->avg('rating') ?? 0, 2),
            'rating_count' => $approved->count(),
        ]);
    }
}
