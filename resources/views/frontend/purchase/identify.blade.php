@extends('layouts.guest')

@section('title', 'Continue to Checkout')

@section('content')
<div class="auth-card mx-auto p-5" style="max-width:480px;">
    <div class="text-center mb-4">
        <div class="feature-icon mx-auto mb-3" style="width:60px;height:60px;font-size:26px;"><i class="bi bi-phone"></i></div>
        <h4 class="mb-1">Let's get you checked out</h4>
        <p class="text-muted small mb-0">Enter your mobile number to continue.</p>
    </div>

    @include('frontend.purchase._product-summary')

    <form method="POST" action="{{ route('purchase.identify.check') }}">
        @csrf
        <x-form.input name="phone" type="tel" label="Mobile Number" placeholder="+1 555 123 4567" required autofocus />
        <button class="btn btn-brand w-100" type="submit">Continue</button>
    </form>

    <p class="text-center small text-muted mt-4 mb-0">
        We'll check if you already have an account. If not, we'll help you create one in the next step.
    </p>
</div>
@endsection
