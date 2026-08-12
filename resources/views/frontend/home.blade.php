@extends('layouts.frontend')

@section('title', 'Home')

@section('content')
<section class="hero-section" style="background: var(--gradient-hero); padding: 110px 0 70px;">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="eyebrow"><i class="bi bi-stars"></i> #1 Platform for Online Learning</span>
                <h1 style="font-size: clamp(32px, 4.6vw, 54px); line-height: 1.1;">Learn new skills <span class="text-primary-brand">online</span> with expert instructors</h1>
                <p class="fs-5 mb-4" style="max-width:520px;">Explore courses and books across programming, design, business, marketing and more — taught by industry professionals, available anytime.</p>
                <form action="{{ route('courses.index') }}" method="GET" class="d-flex mb-4" style="max-width:520px;">
                    <input type="text" name="search" class="form-control form-control-custom" placeholder="What do you want to learn today?" style="border-radius: var(--radius-pill) 0 0 var(--radius-pill);">
                    <button class="btn btn-brand" style="border-radius: 0 999px 999px 0;">Search</button>
                </form>
                <div class="d-flex gap-3">
                    <a href="{{ route('courses.index') }}" class="btn btn-brand">Browse Courses</a>
                    <a href="{{ route('books.index') }}" class="btn btn-outline-brand">Explore Books</a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="https://picsum.photos/seed/heroedu/640/480" alt="Students learning online" class="img-fluid rounded-4 shadow-lg">
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3" data-aos="fade-up">
            <div>
                <span class="eyebrow">Categories</span>
                <h2 class="section-title">Browse top categories</h2>
            </div>
            <a href="{{ route('courses.index') }}" class="btn btn-outline-brand">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($courseCategories as $category)
                <div class="col-lg-2 col-md-3 col-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 6) * 80 }}">
                    <a href="{{ route('courses.index', ['category_id' => $category->id]) }}" class="text-decoration-none">
                        <div class="feature-card text-center">
                            <div class="feature-icon mx-auto" style="background:{{ $category->color }}20; color:{{ $category->color }};"><i class="bi {{ $category->icon }}"></i></div>
                            <h6 class="mb-0">{{ $category->name }}</h6>
                            <small class="text-muted">{{ $category->courses_count }} Courses</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-padding" style="background:#F1F5F9;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3" data-aos="fade-up">
            <div>
                <span class="eyebrow">Featured Courses</span>
                <h2 class="section-title">Our most popular courses</h2>
            </div>
            <a href="{{ route('courses.index') }}" class="btn btn-outline-brand">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($featuredCourses as $course)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                    <x-course-card :course="$course" />
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3" data-aos="fade-up">
            <div>
                <span class="eyebrow">Featured Books</span>
                <h2 class="section-title">Expand your library</h2>
            </div>
            <a href="{{ route('books.index') }}" class="btn btn-outline-brand">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($featuredBooks as $book)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                    <x-book-card :book="$book" />
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-padding" style="background:#F1F5F9;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="eyebrow">Testimonials</span>
            <h2 class="section-title">What our students say</h2>
        </div>
        <div class="row g-4">
            @foreach($testimonials as $testimonial)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                    <div class="feature-card h-100">
                        <div class="rating-stars mb-2">{!! star_rating($testimonial->rating) !!}</div>
                        <p>"{{ $testimonial->content }}"</p>
                        <div class="d-flex align-items-center gap-3 mt-3">
                            <img src="{{ $testimonial->photo ? asset('storage/'.$testimonial->photo) : 'https://i.pravatar.cc/60?u='.$testimonial->id }}" class="avatar-sm" alt="{{ $testimonial->name }}">
                            <div><h6 class="mb-0">{{ $testimonial->name }}</h6><small class="text-muted">{{ $testimonial->designation }}</small></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="container my-5">
    <div class="rounded-4 text-center text-white p-5" style="background: var(--gradient-primary);" data-aos="zoom-in">
        <h2 class="text-white mb-3">Ready to start learning something new?</h2>
        <p class="mb-4 mx-auto" style="max-width:560px; color:rgba(255,255,255,.85);">Join our growing community of learners today.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('courses.index') }}" class="btn btn-light fw-semibold px-4">Browse Courses</a>
            <a href="{{ route('register') }}" class="btn btn-outline-light px-4">Register Now</a>
        </div>
    </div>
</section>
@endsection
