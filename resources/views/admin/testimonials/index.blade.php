@extends('layouts.app')

@section('title', 'Testimonials')
@section('page-title', 'Testimonials')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#createTestimonialModal"><i class="bi bi-plus"></i> New Testimonial</button>
</div>

<div class="row g-3">
    @forelse($testimonials as $testimonial)
        <div class="col-md-6 col-lg-4">
            <div class="filter-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $testimonial->photo ? asset('storage/'.$testimonial->photo) : 'https://i.pravatar.cc/60?u='.$testimonial->id }}" class="avatar-sm" alt="{{ $testimonial->name }}">
                        <div><span class="d-block small fw-semibold">{{ $testimonial->name }}</span><span class="text-muted small">{{ $testimonial->designation }}</span></div>
                    </div>
                    <x-status-badge :status="$testimonial->status ? 'active' : 'inactive'" />
                </div>
                <div class="rating-stars small mb-2">{!! star_rating($testimonial->rating) !!}</div>
                <p class="small text-muted mb-3">{{ \Illuminate\Support\Str::limit($testimonial->content, 120) }}</p>
                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" data-confirm="Delete this testimonial?">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm-pill btn-outline-danger w-100">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state"><i class="bi bi-chat-quote"></i><p>No testimonials yet.</p></div>
    @endforelse
</div>
<div class="mt-4">{{ $testimonials->links() }}</div>

<div class="modal fade" id="createTestimonialModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
        <div class="modal-body p-4">
            <h5 class="mb-3">New Testimonial</h5>
            <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
                @csrf
                <x-form.input name="name" label="Name" required />
                <x-form.input name="designation" label="Designation" />
                <div class="mb-3">
                    <label class="form-label-custom">Photo</label>
                    <input type="file" name="photo" class="form-control form-control-custom" accept="image/*">
                </div>
                <x-form.select name="rating" label="Rating" :options="[5 => '5 Stars', 4 => '4 Stars', 3 => '3 Stars']" required />
                <x-form.textarea name="content" label="Testimonial" rows="4" required />
                <button class="btn btn-brand w-100">Add Testimonial</button>
            </form>
        </div>
    </div></div>
</div>
@endsection
