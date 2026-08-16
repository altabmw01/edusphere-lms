@extends('layouts.app')

@section('title', $course->title)
@section('page-title', 'Continue Learning')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="filter-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ $course->title }}</h5>
                @if($enrollment->progress_percent >= 100)
                    <a href="{{ route('student.certificates.index') }}" class="btn btn-brand btn-sm-pill"><i class="bi bi-patch-check me-1"></i> View Certificate</a>
                @endif
            </div>
            <div class="progress progress-thin mb-2"><div class="progress-bar" style="width:{{ $enrollment->progress_percent }}%;"></div></div>
            <small class="text-muted">{{ $enrollment->progress_percent }}% complete</small>
        </div>

        <div class="accordion" id="lessonAccordion">
            @foreach($course->sections as $section)
                <div class="card-brand mb-3 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#lsec{{ $section->id }}">
                            {{ $section->title }}
                        </button>
                    </h2>
                    <div id="lsec{{ $section->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#lessonAccordion">
                        <div class="accordion-body p-3">
                            @foreach($section->lessons as $lesson)
                                @php($isDone = in_array($lesson->id, $completedLessonIds))
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                    <span class="cursor-pointer" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#lessonModal{{ $lesson->id }}">
                                        <i class="bi {{ $isDone ? 'bi-check-circle-fill text-success' : 'bi-play-circle text-primary-brand' }} me-2"></i>
                                        {{ $lesson->title }}
                                        <span class="badge bg-brand-light text-primary-brand ms-2">{{ ucfirst($lesson->type) }}</span>
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">{{ $lesson->duration_minutes }}m</span>
                                        @unless($isDone)
                                            <form action="{{ route('student.lessons.complete', $lesson) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-sm-pill btn-outline-brand">Mark Complete</button>
                                            </form>
                                        @endunless
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-lg-4">
        <div class="filter-card">
            <img src="{{ $course->thumbnail_url }}" class="rounded-3 mb-3 w-100" style="height:140px;object-fit:cover;" alt="{{ $course->title }}">
            <h6 class="mb-1">{{ $course->title }}</h6>
            <p class="small text-muted mb-3">{{ $enrollment?->batch?->teacher?->name ?? 'No teacher assigned yet' }}</p>
            <ul class="list-unstyled small text-muted mb-0">
                <li class="mb-2"><i class="bi bi-collection-play me-2"></i>{{ $course->lessons_count }} lessons</li>
                <li class="mb-2"><i class="bi bi-clock me-2"></i>{{ duration_for_humans($course->duration_minutes) }}</li>
                @if($course->has_certificate)<li><i class="bi bi-patch-check me-2"></i>Certificate on completion</li>@endif
            </ul>
        </div>
    </div>
</div>

{{-- Lesson modals rendered flat here, outside the accordion/column nesting
     above, so Bootstrap's fixed-position modal is never trapped inside a
     constrained ancestor. --}}
@foreach($course->sections as $section)
    @foreach($section->lessons as $lesson)
        <x-lesson-viewer-modal :lesson="$lesson" />
    @endforeach
@endforeach
@endsection
