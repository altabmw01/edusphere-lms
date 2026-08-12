@props(['book'])

<div class="book-card">
    <div class="card-thumb-wrap">
        <a href="{{ route('books.show', $book->slug) }}"><img src="{{ $book->cover_url }}" alt="{{ $book->title }}"></a>
        <span class="badge-category">{{ $book->category?->name }}</span>
        @if($book->discount_percent > 0)
            <span class="badge-discount">-{{ $book->discount_percent }}%</span>
        @endif
    </div>
    <div class="card-body-custom">
        <h3 class="course-title"><a href="{{ route('books.show', $book->slug) }}">{{ $book->title }}</a></h3>
        <p class="small text-muted mb-2">by {{ $book->author }}</p>
        <div class="d-flex align-items-center gap-2 small text-muted mb-3">
            <span class="rating-stars">{!! star_rating((float) $book->rating_avg) !!}</span>
            <span>{{ number_format($book->rating_avg, 1) }} ({{ $book->rating_count }})</span>
        </div>
        <div class="price-row">
            <div>
                @if($book->discount_price)<span class="price-old">{{ money($book->price) }}</span>@endif
                <span class="price-current">{{ money($book->final_price) }}</span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 px-3 pb-3">
        <a href="{{ route('books.show', $book->slug) }}" class="btn btn-outline-brand btn-sm-pill flex-fill">Details</a>
        <form action="{{ route('cart.store') }}" method="POST" class="flex-fill">
            @csrf
            <input type="hidden" name="type" value="book">
            <input type="hidden" name="id" value="{{ $book->id }}">
            <button class="btn btn-brand btn-sm-pill w-100" type="submit">Add to Cart</button>
        </form>
    </div>
</div>
