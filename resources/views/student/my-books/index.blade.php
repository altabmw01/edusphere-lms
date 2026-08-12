@extends('layouts.app')

@section('title', 'My Books')
@section('page-title', 'My Books')

@section('content')
<div class="row g-4">
    @forelse($purchases as $purchase)
        <div class="col-lg-3 col-md-4 col-6">
            <div class="book-card">
                <div class="card-thumb-wrap"><img src="{{ $purchase->book->cover_url }}" alt="{{ $purchase->book->title }}"></div>
                <div class="card-body-custom">
                    <h3 class="course-title">{{ \Illuminate\Support\Str::limit($purchase->book->title, 40) }}</h3>
                    <p class="small text-muted mb-0">{{ $purchase->book->author }}</p>
                </div>
                <div class="d-flex gap-2 px-3 pb-3">
                    <a href="{{ route('books.show', $purchase->book->slug) }}" class="btn btn-outline-brand btn-sm-pill flex-fill">Details</a>
                    <a href="{{ route('student.my-books.download', $purchase->book) }}" class="btn btn-brand btn-sm-pill flex-fill"><i class="bi bi-download"></i></a>
					@if($purchase->batch_id)
                        <a href="{{ route('student.my-books.class', $purchase->book) }}" class="btn btn-outline-brand btn-sm-pill" title="Class Link"><i class="bi bi-camera-video"></i></a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state"><i class="bi bi-journal-bookmark"></i><p>You haven't purchased any books yet.</p><a href="{{ route('books.index') }}" class="btn btn-brand">Browse Books</a></div>
        </div>
    @endforelse
</div>
<div class="mt-4">{{ $purchases->links() }}</div>
@endsection
