@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="auth-card mx-auto" style="max-width:960px;">
    <div class="row g-0">
        <div class="col-lg-5 d-none d-lg-block">
            <div class="auth-side h-100">
                <span class="eyebrow" style="background:rgba(255,255,255,.15); color:#fff;"><i class="bi bi-mortarboard"></i> EduSphere</span>
                <h3 class="text-white mb-3">Welcome back, keep learning.</h3>
                <p class="mb-0" style="color:rgba(255,255,255,.85);">Log in to pick up where you left off and continue building new skills.</p>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="p-5">
                <h3 class="mb-1">Login to your account</h3>
                <p class="text-muted mb-4">Don't have an account? <a href="{{ route('register') }}" class="text-primary-brand fw-semibold">Register here</a></p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <x-form.input name="login" type="text" label="Email or Phone Number" placeholder="you@example.com or +1 555 123 4567" required autofocus />

                    <div class="mb-3">
                        <label for="password" class="form-label-custom">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control form-control-custom @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    @error('login')<div class="alert alert-danger py-2 small">{{ $message }}</div>@enderror

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="small text-primary-brand">Forgot password?</a>
                    </div>

                    <button class="btn btn-brand w-100" type="submit">Login</button>
                </form>

                <p class="small text-muted mt-4 mb-0">
                    Demo accounts (password: <code>password</code>):<br>
                    admin@edusphere.test &middot; manager@edusphere.test &middot; sarah.mitchell@edusphere.test &middot; student@edusphere.test
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
