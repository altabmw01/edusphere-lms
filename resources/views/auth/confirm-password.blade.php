@extends('layouts.guest')

@section('title', 'Confirm Password')

@section('content')
<div class="auth-card mx-auto p-5" style="max-width:460px;">
    <h3 class="mb-1">Confirm your password</h3>
    <p class="text-muted mb-4">This is a secure area. Please confirm your password before continuing.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-4">
            <label for="password" class="form-label-custom">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" class="form-control form-control-custom @error('password') is-invalid @enderror" required autofocus>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-brand w-100" type="submit">Confirm</button>
    </form>
</div>
@endsection
