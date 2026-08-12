@extends('layouts.guest')

@section('title', 'Enter Your Password')

@section('content')
<div class="auth-card mx-auto p-5" style="max-width:480px;">
    <div class="text-center mb-4">
        <img src="{{ $identifiedUser->avatarUrl() }}" class="avatar-md mx-auto mb-3" style="display:block;" alt="{{ $identifiedUser->name }}">
        <h4 class="mb-1">Welcome back, {{ explode(' ', $identifiedUser->name)[0] }}!</h4>
        <p class="text-muted small mb-0">Enter your password to continue to checkout.</p>
    </div>

    @include('frontend.purchase._product-summary')

    <form method="POST" action="{{ route('purchase.password.check') }}">
        @csrf
        <div class="mb-3">
            <label for="password" class="form-label-custom">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" class="form-control form-control-custom @error('password') is-invalid @enderror" required autofocus>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-brand w-100" type="submit">Continue to Checkout</button>
    </form>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('purchase.identify') }}" class="small text-muted"><i class="bi bi-arrow-left me-1"></i> Use a different number</a>
        <a href="{{ route('password.request') }}" class="small text-primary-brand">Forgot password?</a>
    </div>
</div>
@endsection
