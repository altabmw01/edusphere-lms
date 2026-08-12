@extends('layouts.frontend')

@section('title', $book->title)

@section('content')
<header class="pt-4 pb-3" style="background: var(--gradient-hero);">
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('books.index') }}">Books</a></li>
                <li class="breadcrumb-item active">{{ $book->title }}</li>
            </ol>
        </nav>
    </div>
</header>

<section class="section-padding pt-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4" data-aos="fade-right">
                <img src="{{ $book->cover_url }}" class="img-fluid rounded-4 shadow-sm" alt="{{ $book->title }}">
            </div>
            <div class="col-lg-5" data-aos="fade-up">
                <span class="badge bg-brand-light text-primary-brand mb-3">{{ $book->category?->name }}</span>
                <h1 class="mb-2" style="font-size: clamp(22px,2.8vw,32px);">{{ $book->title }}</h1>
                <p class="text-muted mb-3">by <strong>{{ $book->author }}</strong></p>
                <div class="mb-3"><span class="rating-stars">{!! star_rating((float) $book->rating_avg) !!}</span> {{ number_format($book->rating_avg, 1) }} ({{ $book->rating_count }} reviews)</div>
                <p class="text-muted">{{ $book->description }}</p>

                <div class="row g-2 small text-muted mt-2">
                    <div class="col-6"><i class="bi bi-file-earmark-text me-1"></i> {{ $book->pages }} pages</div>
                    <div class="col-6"><i class="bi bi-translate me-1"></i> {{ $book->language }}</div>
                    @if($book->publisher)<div class="col-6"><i class="bi bi-building me-1"></i> {{ $book->publisher }}</div>@endif
                    @if($book->edition)<div class="col-6"><i class="bi bi-bookmark me-1"></i> {{ $book->edition }}</div>@endif
                    @if($book->isbn)<div class="col-6"><i class="bi bi-upc me-1"></i> ISBN {{ $book->isbn }}</div>@endif
                </div>
            </div>
            <div class="col-lg-3" data-aos="fade-left">
                <div class="filter-card">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="price-current fs-3">{{ money($book->final_price) }}</span>
                        @if($book->discount_price)<span class="price-old fs-6">{{ money($book->price) }}</span>@endif
                    </div>
                    <div class="d-grid gap-2">
                        @auth
                            @if($isPurchased)
                                <a href="{{ route('student.my-books.download', $book) }}" class="btn btn-brand"><i class="bi bi-download me-1"></i> Download</a>
                            @else
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="book">
                                    <input type="hidden" name="id" value="{{ $book->id }}">
                                    <button class="btn btn-brand w-100" type="submit">Add to Cart</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-brand">Login to Purchase</a>
                        @endauth
                    </div>
                    <ul class="list-unstyled small text-muted mt-3 mb-0">
                        <li class="mb-2"><i class="bi bi-file-earmark-pdf me-2"></i>PDF format</li>
                        <li class="mb-2"><i class="bi bi-download me-2"></i>Instant download</li>
                        <li><i class="bi bi-arrow-repeat me-2"></i>Free lifetime updates</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-5" data-aos="fade-up">
            <h4 class="mb-4">Reader Reviews</h4>
            <div class="row g-3">
                @forelse($book->approvedReviews()->with('user')->latest()->limit(4)->get() as $review)
                    <div class="col-md-6">
                        <div class="card-brand p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="{{ $review->user->avatarUrl() }}" class="avatar-sm" alt="{{ $review->user->name }}">
                                <div><h6 class="mb-0">{{ $review->user->name }}</h6><span class="rating-stars small">{!! star_rating($review->rating) !!}</span></div>
                            </div>
                            <p class="mb-0 text-muted">{{ $review->comment }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No reviews yet.</p>
                @endforelse
            </div>

            @auth
                @if($isPurchased)
                    <form action="{{ route('student.reviews.books.store', $book) }}" method="POST" class="card-brand p-3 mt-4" style="max-width:600px;">
                        @csrf
                        <h6 class="mb-3">Write a Review</h6>
                        <select name="rating" class="form-select form-control-custom mb-2" required>
                            <option value="">Rating</option>
                            @for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>@endfor
                        </select>
                        <textarea name="comment" class="form-control form-control-custom mb-2" rows="3" placeholder="Share your thoughts..." required></textarea>
                        <button class="btn btn-brand btn-sm-pill">Submit Review</button>
                    </form>
                @endif
            @endauth
        </div>

        @if($related->isNotEmpty())
        <div class="mt-5" data-aos="fade-up">
            <h4 class="mb-4">Related Books</h4>
            <div class="row g-4">
                @foreach($related as $relatedBook)
                    <div class="col-lg-4 col-md-6"><x-book-card :book="$relatedBook" /></div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
