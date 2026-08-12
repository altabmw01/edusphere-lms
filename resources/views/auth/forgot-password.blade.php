@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-card mx-auto p-5" style="max-width:480px;">
    <h3 class="mb-1">Forgot your password?</h3>
    <p class="text-muted mb-4">Enter your email and we'll send you a link to reset it.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-form.input name="email" type="email" label="Email Address" required autofocus />
        <button class="btn btn-brand w-100" type="submit">Send Password Reset Link</button>
    </form>

    <p class="text-center mt-4 mb-0"><a href="{{ route('login') }}" class="text-primary-brand small"><i class="bi bi-arrow-left me-1"></i> Back to login</a></p>
</div>
@endsection
