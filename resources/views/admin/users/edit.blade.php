@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="filter-card" style="max-width:600px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <img src="{{ $editUser->avatarUrl() }}" class="avatar-md" alt="{{ $editUser->name }}">
        <div><h6 class="mb-0">{{ $editUser->name }}</h6><span class="text-muted small text-capitalize">{{ $editUser->role }}</span></div>
    </div>
    <form method="POST" action="{{ route('admin.users.update', $editUser) }}">
        @csrf @method('PUT')
        <x-form.input name="name" label="Full Name" :value="$editUser->name" required />
        <x-form.input name="email" type="email" label="Email Address" :value="$editUser->email" required />
        <x-form.input name="phone" type="tel" label="Phone Number" :value="$editUser->phone" />
        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked($editUser->is_active)>
            <label class="form-check-label small" for="is_active">Account Active</label>
        </div>
        <button class="btn btn-brand w-100">Save Changes</button>
    </form>
</div>
@endsection
