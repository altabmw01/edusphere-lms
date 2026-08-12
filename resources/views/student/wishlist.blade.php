@extends('layouts.app')

@section('title', 'Wishlist')
@section('page-title', 'Wishlist')

@section('content')
<div class="row g-4">
    @forelse($wishlists as $wishlist)
        @php($item = $wishlist->wishlistable)
        @continue(!$item)
        @php($isCourse = $wishlist->wishlistable_type === \App\Models\Course::class)
        <div class="col-lg-3 col-md-4 col-6">
            <div class="{{ $isCourse ? 'course-card' : 'book-card' }}">
                <div class="card-thumb-wrap">
                    <a href="{{ $isCourse ? route('courses.show', $item->slug) : route('books.show', $item->slug) }}">
                        <img src="{{ $isCourse ? $item->thumbnail_url : $item->cover_url }}" alt="{{ $item->title }}">
                    </a>
                </div>
                <div class="card-body-custom">
                    <h3 class="course-title">{{ \Illuminate\Support\Str::limit($item->title, 40) }}</h3>
                    <span class="price-current">{{ money($item->final_price) }}</span>
                </div>
                <div class="d-flex gap-2 px-3 pb-3">
                    <form action="{{ route('cart.store') }}" method="POST" class="flex-fill">
                        @csrf
                        <input type="hidden" name="type" value="{{ $isCourse ? 'course' : 'book' }}">
                        <input type="hidden" name="id" value="{{ $item->id }}">
                        <button class="btn btn-brand btn-sm-pill w-100">Add to Cart</button>
                    </form>
                    <form action="{{ route('student.wishlist.destroy', $wishlist) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state"><i class="bi bi-heart"></i><p>Your wishlist is empty.</p><a href="{{ route('courses.index') }}" class="btn btn-brand">Browse Courses</a></div>
        </div>
    @endforelse
</div>
@endsection
