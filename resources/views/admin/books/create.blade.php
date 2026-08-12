@extends('layouts.app')

@section('title', 'New Book')
@section('page-title', 'New Book')

@section('content')
<form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
    @csrf
    @include('partials.forms.book-form')
</form>
@endsection
