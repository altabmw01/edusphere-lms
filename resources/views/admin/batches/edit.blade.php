@extends('layouts.app')
@section('title', 'Edit Batch')
@section('page-title', 'Edit Batch')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.batches.assign-students', $batch) }}" class="btn btn-outline-brand"><i class="bi bi-person-plus me-1"></i> Assign Students ({{ $batch->enrolled_count }}/{{ $batch->student_limit }})</a>
</div>
<form method="POST" action="{{ route('admin.batches.update', $batch) }}">
    @csrf @method('PUT')
    @include('partials.forms.batch-form', ['batch' => $batch])
</form>
@endsection
