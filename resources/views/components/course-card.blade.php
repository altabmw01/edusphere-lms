@props(['course'])

<div class="course-card">
    <div class="card-thumb-wrap">
        <a href="{{ route('courses.show', $course->slug) }}"><img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"></a>
        <span class="badge-category">{{ $course->category?->name }}</span>
        @if($course->discount_percent > 0)
            <span class="badge-discount">-{{ $course->discount_percent }}%</span>
        @endif
    </div>
    <div class="card-body-custom">
        <h3 class="course-title"><a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a></h3>
        <div class="d-flex align-items-center gap-2 small text-muted mb-2">
            <span class="rating-stars">{!! star_rating((float) $course->rating_avg) !!}</span>
            <span>{{ number_format($course->rating_avg, 1) }} ({{ $course->rating_count }})</span>
        </div>
        <div class="d-flex gap-3 small text-muted mb-3">
            <span><i class="bi bi-clock"></i> {{ duration_for_humans($course->duration_minutes) }}</span>
            <span><i class="bi bi-collection-play"></i> {{ $course->lessons_count }} lessons</span>
        </div>
        <div class="price-row">
            <div>
                @if($course->discount_price)<span class="price-old">{{ money($course->price) }}</span>@endif
                <span class="price-current">{{ money($course->final_price) }}</span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 px-3 pb-3">
        <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-outline-brand btn-sm-pill flex-fill">Details</a>
        <form action="{{ route('cart.store') }}" method="POST" class="flex-fill">
            @csrf
            <input type="hidden" name="type" value="course">
            <input type="hidden" name="id" value="{{ $course->id }}">
            <button class="btn btn-brand btn-sm-pill w-100" type="submit">Add to Cart</button>
        </form>
    </div>
</div>
