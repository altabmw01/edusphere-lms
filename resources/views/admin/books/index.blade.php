@extends('layouts.app')

@section('title', 'Books')
@section('page-title', 'Books')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-custom" placeholder="Search books..." style="width:260px;">
        <select name="status" class="form-select form-control-custom" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
            <option value="published" @selected(request('status') === 'published')>Published</option>
        </select>
        <button class="btn btn-outline-brand" type="submit">Filter</button>
    </form>
    <a href="{{ route('admin.books.create') }}" class="btn btn-brand"><i class="bi bi-plus"></i> New Book</a>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Book</th><th>Author</th><th>Price</th><th>Sold</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($books as $book)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $book->cover_url }}" width="36" height="48" class="rounded-2" style="object-fit:cover;" alt="{{ $book->title }}">
                            <span>{{ \Illuminate\Support\Str::limit($book->title, 40) }}</span>
                        </div>
                    </td>
                    <td>{{ $book->author }}</td>
                    <td>{{ money($book->final_price) }}</td>
                    <td>{{ number_format($book->sales_count) }}</td>
                    <td><x-status-badge :status="$book->status" /></td>
                    <td class="text-end">
                        <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-icon-circle"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="d-inline" data-confirm="Delete this book?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No books found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $books->links() }}</div>
@endsection
