<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Services\BookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(protected BookService $bookService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Book::class);

        return view('manager.books.index', [
            'books' => $this->bookService->forAdmin($request->only(['search', 'status'])),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Book::class);

        return view('manager.books.create', [
            'categories' => Category::type('book')->active()->ordered()->get(),
        ]);
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $book = $this->bookService->create(
            $request->safe()->except(['cover', 'pdf']),
            $request->user(),
            $request->file('cover'),
            $request->file('pdf'),
        );

        return redirect()->route('manager.books.edit', $book)->with('status', 'Book created successfully.');
    }

    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        return view('manager.books.edit', [
            'book' => $book,
            'categories' => Category::type('book')->active()->ordered()->get(),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $this->bookService->update(
            $book,
            $request->safe()->except(['cover', 'pdf']),
            $request->file('cover'),
            $request->file('pdf'),
        );

        return back()->with('status', 'Book updated successfully.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);
        $this->bookService->delete($book);

        return redirect()->route('manager.books.index')->with('status', 'Book deleted.');
    }
}
