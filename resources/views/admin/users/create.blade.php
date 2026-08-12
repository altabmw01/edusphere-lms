@extends('layouts.app')

@section('title', 'New Staff Account')
@section('page-title', 'New Staff Account')

@section('content')
<div class="filter-card" style="max-width:600px;">
    <p class="text-muted small mb-4"><i class="bi bi-info-circle me-1"></i> Only Admin, Teacher, and Manager accounts can be created here. Students self-register.</p>
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <x-form.input name="name" label="Full Name" required />
        <x-form.input name="email" type="email" label="Email Address" required />
        <x-form.input name="phone" type="tel" label="Phone Number" />
        <x-form.select name="role" label="Role" :options="['teacher' => 'Teacher', 'manager' => 'Manager', 'admin' => 'Admin']" required />
        <div class="mb-3">
            <label class="form-label-custom">Password</label>
            <input type="password" name="password" class="form-control form-control-custom" required>
        </div>
        <div class="mb-4">
            <label class="form-label-custom">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control form-control-custom" required>
        </div>
        <button class="btn btn-brand w-100">Create Account</button>
    </form>
</div>
@endsection
