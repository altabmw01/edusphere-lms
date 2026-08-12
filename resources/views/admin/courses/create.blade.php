@extends('layouts.app')

@section('title', 'New Course')
@section('page-title', 'New Course')

@section('content')
<form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
    @csrf
    @include('partials.forms.course-form')
</form>
@endsection
