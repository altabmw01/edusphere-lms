<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function storeCourse(Request $request, Course $course): RedirectResponse
    {
        abort_unless(Auth::user()->hasPurchasedCourse($course->id), 403, 'You must purchase this course before reviewing it.');

        $this->storeReview($request, $course);

        return back()->with('status', 'Thanks! Your review has been submitted for approval.');
    }

    public function storeBook(Request $request, Book $book): RedirectResponse
    {
        abort_unless(Auth::user()->hasPurchasedBook($book->id), 403, 'You must purchase this book before reviewing it.');

        $this->storeReview($request, $book);

        return back()->with('status', 'Thanks! Your review has been submitted for approval.');
    }

    protected function storeReview(Request $request, Course|Book $reviewable): void
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'reviewable_type' => $reviewable::class,
                'reviewable_id' => $reviewable->id,
            ],
            [
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'status' => 'pending',
                'reply' => null,
                'replied_at' => null,
            ]
        );
    }
}
