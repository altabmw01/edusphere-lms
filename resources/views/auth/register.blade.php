@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="auth-card mx-auto" style="max-width:960px;">
    <div class="row g-0">
        <div class="col-lg-5 d-none d-lg-block">
            <div class="auth-side h-100">
                <span class="eyebrow" style="background:rgba(255,255,255,.15); color:#fff;"><i class="bi bi-mortarboard"></i> EduSphere</span>
                <h3 class="text-white mb-3">Join thousands of learners today.</h3>
                <p class="mb-0" style="color:rgba(255,255,255,.85);">Create a free account and get instant access to our full catalog of courses and books.</p>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="p-5">
                <h3 class="mb-1">Create your account</h3>
                <p class="text-muted mb-4">Already have an account? <a href="{{ route('login') }}" class="text-primary-brand fw-semibold">Login here</a></p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <x-form.input name="name" label="Full Name" required autofocus />
                    <x-form.input name="email" type="email" label="Email Address" required />
                    <x-form.input name="phone" type="tel" label="Phone Number" />

                    <div class="mb-3">
                        <label for="password" class="form-label-custom">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control form-control-custom @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label-custom">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-custom" required>
                    </div>

                    <button class="btn btn-brand w-100" type="submit">Create Account</button>

                    <p class="small text-muted mt-3 mb-0">Teacher, Manager, and Admin accounts are created by EduSphere staff — <a href="{{ route('contact.index') }}" class="text-primary-brand">contact us</a> to apply as an instructor.</p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
