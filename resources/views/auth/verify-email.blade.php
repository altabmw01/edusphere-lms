@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
<div class="auth-card mx-auto p-5 text-center" style="max-width:520px;">
    <div class="feature-icon mx-auto mb-3" style="width:64px;height:64px;font-size:28px;"><i class="bi bi-envelope-paper"></i></div>
    <h3 class="mb-2">Verify your email address</h3>
    <p class="text-muted mb-4">Thanks for signing up! Before getting started, please verify your email by clicking the link we just emailed to you. If you didn't receive the email, we'll gladly send another.</p>

    @if(session('status') === 'verification-link-sent')
        <div class="alert alert-success mb-4">A new verification link has been sent to your email address.</div>
    @endif

    <div class="d-flex gap-3 justify-content-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-brand">Resend Verification Email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-brand">Log Out</button>
        </form>
    </div>
</div>
@endsection
