@extends('layouts.guest')

@section('title', 'Create Your Account')

@section('content')
<div class="auth-card mx-auto p-5" style="max-width:520px;">
    <div class="text-center mb-4">
        <div class="feature-icon mx-auto mb-3" style="width:60px;height:60px;font-size:26px;"><i class="bi bi-person-plus"></i></div>
        <h4 class="mb-1">Create your account</h4>
        <p class="text-muted small mb-0">We didn't find an account with that number — let's create one so you can check out.</p>
    </div>

    @include('frontend.purchase._product-summary')

    <form method="POST" action="{{ route('purchase.register.store') }}">
        @csrf
        <x-form.input name="name" label="Full Name" required autofocus />
        <x-form.input name="email" type="email" label="Email Address" required />
        <x-form.input name="phone" type="tel" label="Mobile Number" :value="old('phone', $phone)" required />

        <div class="mb-3">
            <label class="form-label-custom">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control form-control-custom @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="form-label-custom">Confirm Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" class="form-control form-control-custom" required>
        </div>

        <button class="btn btn-brand w-100" type="submit">Create Account &amp; Continue to Checkout</button>
    </form>

    <p class="text-center mt-4 mb-0"><a href="{{ route('purchase.identify') }}" class="small text-muted"><i class="bi bi-arrow-left me-1"></i> Use a different number</a></p>
</div>
@endsection
