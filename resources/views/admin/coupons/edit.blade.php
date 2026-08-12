@extends('layouts.app')
@section('title', 'Edit Coupon')
@section('page-title', 'Edit Coupon')
@section('content')
<form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
    @csrf @method('PUT')
    @include('partials.forms.coupon-form', ['coupon' => $coupon])
</form>
@endsection
