@extends('layouts.frontend')

@section('title', $course->title)

@section('content')
<header class="pt-4 pb-5" style="background: var(--gradient-hero);">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-up">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                <li class="breadcrumb-item active">{{ $course->title }}</li>
            </ol>
        </nav>
        <div class="row align-items-center gy-4">
            <div class="col-lg-7" data-aos="fade-right">
                <span class="badge bg-brand-light text-primary-brand mb-3">{{ $course->category?->name }}</span>
                <h1 class="mb-3" style="font-size: clamp(24px,3.2vw,36px);">{{ $course->title }}</h1>
                <p class="mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 200) }}</p>
                <div class="d-flex flex-wrap gap-4 mb-3">
                    <span><span class="rating-stars">{!! star_rating((float) $course->rating_avg) !!}</span> {{ number_format($course->rating_avg, 1) }} ({{ $course->rating_count }} ratings)</span>
                    <span><i class="bi bi-people me-1"></i> {{ number_format($course->students_count) }} students</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $course->teacher?->avatarUrl() }}" width="40" height="40" class="rounded-circle" alt="{{ $course->teacher?->name }}">
                    <span>Created by <strong>{{ $course->teacher?->name }}</strong></span>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="course-card">
                    <div class="card-thumb-wrap"><img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"></div>
                    <div class="card-body-custom">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="price-current fs-3">{{ money($course->final_price) }}</span>
                            @if($course->discount_price)
                                <span class="price-old fs-6">{{ money($course->price) }}</span>
                                <span class="badge" style="background:var(--accent); color:#fff;">-{{ $course->discount_percent }}%</span>
                            @endif
                        </div>
                        <div class="d-grid gap-2 mb-3">
                            @auth
                                @if($isEnrolled)
                                    <a href="{{ route('student.my-courses.show', $course->slug) }}" class="btn btn-brand">Continue Learning</a>
                                @else
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="course">
                                        <input type="hidden" name="id" value="{{ $course->id }}">
                                        <button class="btn btn-brand w-100" type="submit">Add to Cart</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-brand">Login to Enroll</a>
                            @endauth
                        </div>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li class="mb-2"><i class="bi bi-clock me-2"></i>{{ duration_for_humans($course->duration_minutes) }} on-demand</li>
                            <li class="mb-2"><i class="bi bi-collection-play me-2"></i>{{ $course->lessons_count }} lessons</li>
                            <li class="mb-2"><i class="bi bi-infinity me-2"></i>Full lifetime access</li>
                            @if($course->has_certificate)<li><i class="bi bi-patch-check me-2"></i>Certificate of completion</li>@endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section-padding pt-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                @if($course->what_you_will_learn)
                <div class="mb-5" data-aos="fade-up">
                    <h4 class="mb-3">What You Will Learn</h4>
                    <div class="row g-2">
                        @foreach(explode("\n", $course->what_you_will_learn) as $line)
                            @continue(trim($line) === '')
                            <div class="col-md-6"><p class="mb-2"><i class="bi bi-check-circle-fill text-primary-brand me-2"></i>{{ $line }}</p></div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mb-5" data-aos="fade-up">
                    <h4 class="mb-3">Course Curriculum</h4>
                    <div class="accordion" id="curriculumAccordion">
                        @foreach($course->sections as $section)
                            <div class="card-brand mb-3 overflow-hidden">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#sec{{ $section->id }}">
                                        {{ $section->title }} <span class="text-muted small ms-2">({{ $section->lessons->count() }} lessons)</span>
                                    </button>
                                </h2>
                                <div id="sec{{ $section->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#curriculumAccordion">
                                    <div class="accordion-body p-3">
                                        @foreach($section->lessons as $lesson)
                                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                                <span>
                                                    <i class="bi {{ $lesson->is_preview || $isEnrolled ? 'bi-play-circle text-primary-brand' : 'bi-lock text-muted' }} me-2"></i>
                                                    {{ $lesson->title }}
                                                    @if($lesson->is_preview)<span class="badge bg-brand-light text-primary-brand ms-2">Preview</span>@endif
                                                </span>
                                                <span class="text-muted small">{{ $lesson->duration_minutes }}m</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($course->requirements)
                <div class="mb-5" data-aos="fade-up">
                    <h4 class="mb-3">Requirements</h4>
                    <ul class="text-muted">
                        @foreach(explode("\n", $course->requirements) as $line)
                            @continue(trim($line) === '')
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mb-5" data-aos="fade-up">
                    <h4 class="mb-3">Description</h4>
                    <p class="text-muted">{{ $course->description }}</p>
                </div>

                @if($course->target_audience)
                <div class="mb-5" data-aos="fade-up">
                    <h4 class="mb-3">Who This Course Is For</h4>
                    <p class="text-muted">{{ $course->target_audience }}</p>
                </div>
                @endif

                <div data-aos="fade-up">
                    <h4 class="mb-3">Student Reviews</h4>
                    @forelse($course->approvedReviews()->with('user')->latest()->limit(5)->get() as $review)
                        <div class="card-brand p-3 mb-3">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="{{ $review->user->avatarUrl() }}" class="avatar-sm" alt="{{ $review->user->name }}">
                                <div><h6 class="mb-0">{{ $review->user->name }}</h6><span class="rating-stars small">{!! star_rating($review->rating) !!}</span></div>
                            </div>
                            <p class="mb-0 text-muted">{{ $review->comment }}</p>
                            @if($review->reply)
                                <div class="bg-light rounded-3 p-2 mt-2 small"><strong>EduSphere Team:</strong> {{ $review->reply }}</div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">No reviews yet — be the first to review this course.</p>
                    @endforelse

                    @auth
                        @if($isEnrolled)
                            <form action="{{ route('student.reviews.courses.store', $course) }}" method="POST" class="card-brand p-3 mt-4">
                                @csrf
                                <h6 class="mb-3">Write a Review</h6>
                                <select name="rating" class="form-select form-control-custom mb-2" required>
                                    <option value="">Rating</option>
                                    @for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>@endfor
                                </select>
                                <textarea name="comment" class="form-control form-control-custom mb-2" rows="3" placeholder="Share your experience..." required></textarea>
                                <button class="btn btn-brand btn-sm-pill">Submit Review</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="col-lg-4">
                <div class="filter-card" data-aos="fade-up">
                    <h6 class="fw-bold">Instructor</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $course->teacher?->avatarUrl() }}" class="avatar-md" alt="{{ $course->teacher?->name }}">
                        <div><h6 class="mb-0">{{ $course->teacher?->name }}</h6><small class="text-muted">{{ $course->teacher?->teacherProfile?->headline }}</small></div>
                    </div>
                    <p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($course->teacher?->bio, 160) }}</p>
                </div>
            </div>
        </div>

        @if($related->isNotEmpty())
        <div class="mt-5" data-aos="fade-up">
            <h4 class="mb-4">Related Courses</h4>
            <div class="row g-4">
                @foreach($related as $relatedCourse)
                    <div class="col-lg-4 col-md-6"><x-course-card :course="$relatedCourse" /></div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
