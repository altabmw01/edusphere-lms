<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(protected BookService $bookService)
    {
    }

    public function index(Request $request): View
    {
        $books = $this->bookService->browse($request->only(['search', 'category_id', 'max_price', 'sort']));

        return view('frontend.books.index', [
            'books' => $books,
            'categories' => Category::type('book')->active()->ordered()->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $book = $this->bookService->show($slug);
        abort_unless($book && $book->status === 'published', 404);

        return view('frontend.books.show', [
            'book' => $book,
            'related' => $this->bookService->related($book),
            'isPurchased' => auth()->check() && auth()->user()->hasPurchasedBook($book->id),
        ]);
    }
}
