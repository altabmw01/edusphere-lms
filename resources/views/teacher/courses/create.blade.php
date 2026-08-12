@extends('layouts.app')
@section('title', 'New Course')
@section('page-title', 'New Course')
@section('content')
<div class="alert alert-info mb-4"><i class="bi bi-info-circle me-2"></i>New courses are submitted as "Pending" and reviewed by our team before going live.</div>
<form method="POST" action="{{ route('teacher.courses.store') }}" enctype="multipart/form-data">
    @csrf
    @include('partials.forms.course-form', ['showTeacherField' => false])
</form>
@endsection
