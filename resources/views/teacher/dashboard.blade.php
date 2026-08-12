@extends('layouts.app')

@section('title', 'Teacher Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-primary);"><i class="bi bi-collection-play"></i></div><h4 class="mb-0">{{ $stats['total_courses'] }}</h4><small class="text-muted">My Courses</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-warm);"><i class="bi bi-people"></i></div><h4 class="mb-0">{{ $stats['total_students'] }}</h4><small class="text-muted">Total Students</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#22C55E;"><i class="bi bi-cash-stack"></i></div><h4 class="mb-0">{{ money($stats['revenue']) }}</h4><small class="text-muted">Course Revenue</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#8B5CF6;"><i class="bi bi-star"></i></div><h4 class="mb-0">{{ $stats['pending_reviews'] }}</h4><small class="text-muted">Pending Reviews</small></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-brand">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0">My Courses</h6>
                <a href="{{ route('teacher.courses.create') }}" class="btn btn-brand btn-sm-pill"><i class="bi bi-plus"></i> New Course</a>
            </div>
            <table class="table mb-0">
                <thead><tr><th>Course</th><th>Status</th><th>Students</th><th>Rating</th><th></th></tr></thead>
                <tbody>
                    @forelse($myCourses as $course)
                        <tr>
                            <td>{{ $course->title }}</td>
                            <td><x-status-badge :status="$course->status" /></td>
                            <td>{{ $course->students_count }}</td>
                            <td><span class="rating-stars small">{!! star_rating((float) $course->rating_avg) !!}</span></td>
                            <td><a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-icon-circle"><i class="bi bi-pencil"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">You haven't created any courses yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-brand">
            <div class="p-3 border-bottom"><h6 class="mb-0">Pending Reviews to Reply</h6></div>
            @forelse($pendingReviews as $review)
                <div class="p-3 border-bottom">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="{{ $review->user->avatarUrl() }}" class="avatar-sm" alt="{{ $review->user->name }}">
                        <span class="small fw-semibold">{{ $review->user->name }}</span>
                        <span class="rating-stars small ms-auto">{!! star_rating($review->rating) !!}</span>
                    </div>
                    <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($review->comment, 80) }}</p>
                    <button class="btn btn-sm-pill btn-outline-brand" data-bs-toggle="modal" data-bs-target="#teacherReplyModal{{ $review->id }}">Reply</button>
                </div>

                <div class="modal fade" id="teacherReplyModal{{ $review->id }}" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
                        <div class="modal-body p-4">
                            <h6 class="mb-3">Reply to {{ $review->user->name }}</h6>
                            <form action="{{ route('teacher.reviews.reply', $review) }}" method="POST">
                                @csrf
                                <textarea name="reply" class="form-control form-control-custom mb-3" rows="3" required>{{ $review->reply }}</textarea>
                                <button class="btn btn-brand w-100">Post Reply</button>
                            </form>
                        </div>
                    </div></div>
                </div>
            @empty
                <p class="text-muted small p-3 mb-0">No pending reviews.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
