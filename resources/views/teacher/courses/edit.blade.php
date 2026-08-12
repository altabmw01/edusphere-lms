@extends('layouts.app')
@section('title', 'Edit Course')
@section('page-title', 'Edit Course')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('teacher.courses.curriculum.edit', $course) }}" class="btn btn-outline-brand"><i class="bi bi-list-check me-1"></i> Manage Curriculum</a>
</div>
<form method="POST" action="{{ route('teacher.courses.update', $course) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('partials.forms.course-form', ['course' => $course, 'showTeacherField' => false])
</form>
@endsection
