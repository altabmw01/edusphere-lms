<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with('wishlistable')
            ->latest()
            ->get();

        return view('student.wishlist', ['wishlists' => $wishlists]);
    }

    public function storeCourse(Course $course): RedirectResponse
    {
        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'wishlistable_type' => Course::class,
            'wishlistable_id' => $course->id,
        ]);

        return back()->with('status', 'Added to wishlist.');
    }

    public function storeBook(Book $book): RedirectResponse
    {
        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'wishlistable_type' => Book::class,
            'wishlistable_id' => $book->id,
        ]);

        return back()->with('status', 'Added to wishlist.');
    }

    public function destroy(Wishlist $wishlist): RedirectResponse
    {
        abort_unless($wishlist->user_id === Auth::id(), 403);

        $wishlist->delete();

        return back()->with('status', 'Removed from wishlist.');
    }
}
