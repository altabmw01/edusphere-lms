<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function reply(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('reply', $review);

        $request->validate(['reply' => ['required', 'string', 'max:2000']]);

        $review->update(['reply' => $request->string('reply'), 'replied_at' => now()]);

        return back()->with('status', 'Reply posted.');
    }
}
