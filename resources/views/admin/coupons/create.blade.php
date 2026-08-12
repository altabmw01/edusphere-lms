@extends('layouts.app')
@section('title', 'New Coupon')
@section('page-title', 'New Coupon')
@section('content')
<form method="POST" action="{{ route('admin.coupons.store') }}">
    @csrf
    @include('partials.forms.coupon-form')
</form>
@endsection
