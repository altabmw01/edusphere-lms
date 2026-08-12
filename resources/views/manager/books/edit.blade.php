@extends('layouts.app')
@section('title', 'Edit Book')
@section('page-title', 'Edit Book')
@section('content')
<form method="POST" action="{{ route('manager.books.update', $book) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('partials.forms.book-form', ['book' => $book])
</form>
@endsection
