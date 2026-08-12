@extends('layouts.app')

@section('title', 'Edit Course')
@section('page-title', 'Edit Course')

@section('content')
<form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('partials.forms.course-form', ['course' => $course])
</form>

<div class="filter-card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Curriculum</h6>
        <a href="{{ route('admin.courses.curriculum.edit', $course) }}" class="btn btn-brand btn-sm-pill"><i class="bi bi-list-check me-1"></i> Manage Curriculum</a>
    </div>
    @forelse($course->sections as $section)
        <div class="mb-2">
            <strong class="small">{{ $section->title }}</strong>
            <span class="text-muted small">&mdash; {{ $section->lessons->count() }} lessons</span>
        </div>
    @empty
        <p class="text-muted small mb-0">No curriculum added yet.</p>
    @endforelse
</div>
@endsection
