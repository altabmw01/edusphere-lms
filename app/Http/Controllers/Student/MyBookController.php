<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MyBookController extends Controller
{
	use AuthorizesRequests;
	
    public function index(): View
    {
        $purchases = Auth::user()->bookPurchases()
            ->with('book.category')
            ->latest()
            ->paginate(9);

        return view('student.my-books.index', ['purchases' => $purchases]);
    }

    public function download(Book $book): RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('download', $book);

        $purchase = Auth::user()->bookPurchases()->where('book_id', $book->id)->firstOrFail();
        $purchase->increment('download_count');
        $purchase->update(['last_downloaded_at' => now()]);

        abort_unless($book->pdf_path && Storage::disk('public')->exists($book->pdf_path), 404, 'File not available.');

        return Storage::disk('public')->download($book->pdf_path, $book->slug . '.pdf');
    }
}
