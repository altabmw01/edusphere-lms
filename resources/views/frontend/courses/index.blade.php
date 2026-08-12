@extends('layouts.frontend')

@section('title', 'Courses')

@section('content')
<header class="section-padding pb-0" style="background: var(--gradient-hero); padding-top:60px;">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="section-title">Explore Courses</h1>
        <p class="section-subtitle mx-auto">{{ $courses->total() }} courses across every category.</p>
    </div>
</header>

<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <form method="GET" action="{{ route('courses.index') }}">
                    <div class="filter-card">
                        <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>Search</h6>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-custom" placeholder="Search courses...">
                    </div>
                    <div class="filter-card">
                        <h6 class="fw-bold mb-3">Category</h6>
                        @foreach($categories as $category)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category_id" value="{{ $category->id }}" id="cat{{ $category->id }}" @checked((string) request('category_id') === (string) $category->id) onchange="this.form.submit()">
                                <label class="form-check-label small" for="cat{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="filter-card">
                        <h6 class="fw-bold mb-3">Level</h6>
                        @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'all_levels' => 'All Levels'] as $value => $label)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="level" value="{{ $value }}" id="lvl{{ $value }}" @checked(request('level') === $value) onchange="this.form.submit()">
                                <label class="form-check-label small" for="lvl{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="filter-card mb-0">
                        <h6 class="fw-bold mb-3">Price Range</h6>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control form-control-custom" placeholder="Min">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control form-control-custom" placeholder="Max">
                        </div>
                        <button class="btn btn-brand w-100 mt-3" type="submit">Apply Filters</button>
                        <a href="{{ route('courses.index') }}" class="btn btn-outline-brand w-100 mt-2">Reset</a>
                    </div>
                </form>
            </div>

            <div class="col-lg-9">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <p class="mb-0 text-muted">Showing <strong>{{ $courses->count() }}</strong> of <strong>{{ $courses->total() }}</strong> courses</p>
                    <form method="GET" action="{{ route('courses.index') }}">
                        @foreach(request()->except('sort') as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <select name="sort" class="form-select form-control-custom" style="width:auto;" onchange="this.form.submit()">
                            <option value="popular" @selected(request('sort', 'popular') === 'popular')>Sort by: Popular</option>
                            <option value="newest" @selected(request('sort') === 'newest')>Newest</option>
                            <option value="price_low" @selected(request('sort') === 'price_low')>Price: Low to High</option>
                            <option value="price_high" @selected(request('sort') === 'price_high')>Price: High to Low</option>
                            <option value="rating" @selected(request('sort') === 'rating')>Highest Rated</option>
                        </select>
                    </form>
                </div>

                @if($courses->isEmpty())
                    <div class="empty-state"><i class="bi bi-search"></i><p>No courses match your filters.</p><a href="{{ route('courses.index') }}" class="btn btn-outline-brand">Clear Filters</a></div>
                @else
                    <div class="row g-4">
                        @foreach($courses as $course)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                                <x-course-card :course="$course" />
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5">{{ $courses->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
