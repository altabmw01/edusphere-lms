@extends('layouts.app')

@section('title', 'Edit Course')
@section('page-title', 'Edit Course')

@section('content')
<form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('partials.forms.course-form', ['course' => $course, 'showTeacherField' => true])
</form>

@if($course->sections->isNotEmpty())
<div class="filter-card mt-4">
    <h6 class="fw-bold mb-3">Curriculum Overview <span class="text-muted small fw-normal">(managed by the course teacher)</span></h6>
    @foreach($course->sections as $section)
        <div class="mb-2">
            <strong class="small">{{ $section->title }}</strong>
            <span class="text-muted small">— {{ $section->lessons->count() }} lessons</span>
        </div>
    @endforeach
</div>
@endif
@endsection
