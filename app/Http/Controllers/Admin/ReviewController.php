<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class ReviewController extends Controller
{
    use AuthorizesRequests;
    
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->with(['user', 'reviewable'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', ['reviews' => $reviews]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['status' => 'approved']);
        $this->recalculateRating($review);

        return back()->with('status', 'Review approved.');
    }

    public function reject(Review $review): RedirectResponse
    {
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
