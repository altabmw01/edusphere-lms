@extends('layouts.app')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="row g-4">
    @forelse($enrollments as $enrollment)
        <div class="col-lg-6">
            <div class="course-card flex-row">
                <div class="card-thumb-wrap" style="width:150px;flex-shrink:0;">
                    <img src="{{ $enrollment->course->thumbnail_url }}" alt="{{ $enrollment->course->title }}" style="height:100%;">
                </div>
                <div class="card-body-custom">
                    <span class="badge bg-brand-light text-primary-brand mb-2" style="width:fit-content;">{{ $enrollment->progress_percent >= 100 ? 'Completed' : 'In Progress' }}</span>
                    <h3 class="course-title">{{ $enrollment->course->title }}</h3>
                    <p class="small text-muted mb-2">{{ $enrollment->batch?->teacher?->name ?? 'No teacher assigned yet' }}</p>
                    <div class="progress progress-thin mb-2"><div class="progress-bar" style="width:{{ $enrollment->progress_percent }}%;"></div></div>
                    <small class="text-muted mb-3">{{ $enrollment->progress_percent }}% complete</small>
                    <div class="mt-2 d-flex gap-2">
                        <a href="{{ route('student.my-courses.show', $enrollment->course->slug) }}" class="btn btn-brand flex-fill">
                            {{ $enrollment->progress_percent >= 100 ? 'Review Course' : 'Continue Learning' }}
                        </a>
                        @if($enrollment->batch_id)
                            <a href="{{ route('student.my-courses.class', $enrollment->course->slug) }}" class="btn btn-outline-brand" title="Class Link"><i class="bi bi-camera-video"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state"><i class="bi bi-collection-play"></i><p>You haven't enrolled in any courses yet.</p><a href="{{ route('courses.index') }}" class="btn btn-brand">Browse Courses</a></div>
        </div>
    @endforelse
</div>
<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
