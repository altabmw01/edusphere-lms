@extends('layouts.app')
@section('title', 'New Batch')
@section('page-title', 'New Batch')
@section('content')
<form method="POST" action="{{ route('admin.batches.store') }}">
    @csrf
    @include('partials.forms.batch-form')
</form>
@endsection
