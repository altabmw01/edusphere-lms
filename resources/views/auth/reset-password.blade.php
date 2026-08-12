@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="auth-card mx-auto p-5" style="max-width:480px;">
    <h3 class="mb-1">Reset your password</h3>
    <p class="text-muted mb-4">Choose a new password for your account.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <x-form.input name="email" type="email" label="Email Address" :value="old('email', $request->email)" required autofocus />

        <div class="mb-3">
            <label for="password" class="form-label-custom">New Password <span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" class="form-control form-control-custom @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label-custom">Confirm New Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-custom" required>
        </div>

        <button class="btn btn-brand w-100" type="submit">Reset Password</button>
    </form>
</div>
@endsection
