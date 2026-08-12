@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'Account Settings')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="filter-card">
            <h5 class="mb-4">Profile Information</h5>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PATCH')
                <div class="d-flex align-items-center gap-4 mb-4">
                    <img src="{{ $user->avatarUrl() }}" class="avatar-upload" alt="{{ $user->name }}">
                    <div>
                        <input type="file" name="avatar" class="form-control form-control-custom" accept="image/*">
                        <p class="small text-muted mb-0 mt-1">JPG or PNG, max 2MB.</p>
                        @error('avatar')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><x-form.input name="name" label="Full Name" :value="$user->name" required /></div>
                    <div class="col-md-6"><x-form.input name="email" type="email" label="Email Address" :value="$user->email" required /></div>
                    <div class="col-md-6"><x-form.input name="phone" type="tel" label="Phone Number" :value="$user->phone" /></div>
                    <div class="col-md-6"><x-form.input name="city" label="City" :value="$user->city" /></div>
                    <div class="col-md-6"><x-form.input name="country" label="Country" :value="$user->country" /></div>
                    <div class="col-md-6"><x-form.input name="address" label="Address" :value="$user->address" /></div>
                    <div class="col-12"><x-form.textarea name="bio" label="Biography" rows="3" :value="$user->bio" /></div>
                    <div class="col-md-4"><x-form.input name="social_links[facebook]" label="Facebook URL" :value="$user->social_links['facebook'] ?? ''" /></div>
                    <div class="col-md-4"><x-form.input name="social_links[twitter]" label="Twitter URL" :value="$user->social_links['twitter'] ?? ''" /></div>
                    <div class="col-md-4"><x-form.input name="social_links[linkedin]" label="LinkedIn URL" :value="$user->social_links['linkedin'] ?? ''" /></div>
                </div>
                <button class="btn btn-brand px-4 mt-2" type="submit">Save Changes</button>
            </form>
        </div>

        <div class="filter-card">
            <h5 class="mb-4">Change Password</h5>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Current Password</label>
                        <input type="password" name="current_password" class="form-control form-control-custom @error('current_password') is-invalid @enderror" required>
                        @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">New Password</label>
                        <input type="password" name="password" class="form-control form-control-custom" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-custom" required>
                    </div>
                </div>
                <button class="btn btn-brand px-4 mt-3" type="submit">Update Password</button>
            </form>
        </div>
		<!--
        <div class="filter-card mb-0 border-danger">
            <h5 class="mb-2 text-danger">Delete Account</h5>
            <p class="small text-muted">This will permanently delete your account and all associated data. This action cannot be undone.</p>
            <button class="btn btn-outline-danger btn-sm-pill" type="button" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Delete My Account</button>
        </div>
		-->
    </div>

    <div class="col-lg-4">
        <div class="filter-card text-center">
            <img src="{{ $user->avatarUrl() }}" class="avatar-upload mb-3" alt="{{ $user->name }}">
            <h6 class="mb-0">{{ $user->name }}</h6>
            <small class="text-muted text-capitalize">{{ $user->role }}</small>
            <hr>
            <p class="small text-muted mb-0">Member since {{ $user->created_at->format('M Y') }}</p>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius-lg);">
            <div class="modal-body p-4">
                <h5 class="mb-3">Confirm Account Deletion</h5>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf @method('DELETE')
                    <label class="form-label-custom">Enter your password to confirm</label>
                    <input type="password" name="password" class="form-control form-control-custom mb-3 @error('password', 'userDeletion') is-invalid @enderror" required>
                    @error('password', 'userDeletion')<div class="text-danger small mb-3">{{ $message }}</div>@enderror
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-brand" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
