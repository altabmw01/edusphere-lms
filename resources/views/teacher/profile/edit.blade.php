@extends('layouts.app')

@section('title', 'Teacher Profile')
@section('page-title', 'Teacher Profile')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Public Teacher Profile</h6>
            <p class="small text-muted mb-4">This information appears on your public instructor page and course listings. To update your name, email, or avatar, visit <a href="{{ route('profile.edit') }}">Account Settings</a>.</p>
            <form method="POST" action="{{ route('teacher.profile.update') }}" id="teacherProfileForm">
                @csrf @method('PATCH')
                <x-form.input name="headline" label="Professional Headline" :value="$profile?->headline" hint="e.g. Senior Software Engineer" />
                <x-form.textarea name="biography" label="Biography" rows="5" :value="$profile?->biography" />
                <x-form.input name="experience_years" type="number" label="Years of Experience" :value="$profile?->experience_years" />

                <label class="form-label-custom">Skills (comma-separated)</label>
                <input type="text" class="form-control form-control-custom mb-3" id="skillsInput" value="{{ implode(', ', $profile?->skills ?? []) }}" placeholder="Laravel, PHP, JavaScript">

                <div class="row">
                    <div class="col-md-4"><x-form.input name="social_links[facebook]" label="Facebook URL" :value="$profile?->social_links['facebook'] ?? ''" /></div>
                    <div class="col-md-4"><x-form.input name="social_links[twitter]" label="Twitter URL" :value="$profile?->social_links['twitter'] ?? ''" /></div>
                    <div class="col-md-4"><x-form.input name="social_links[linkedin]" label="LinkedIn URL" :value="$profile?->social_links['linkedin'] ?? ''" /></div>
                </div>
                <button class="btn btn-brand px-4 mt-2" type="submit">Save Profile</button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="filter-card text-center">
            <img src="{{ $user->avatarUrl() }}" class="avatar-upload mb-3" alt="{{ $user->name }}">
            <h6 class="mb-0">{{ $user->name }}</h6>
            <small class="text-muted">{{ $profile?->headline }}</small>
            <hr>
            <div class="row text-center small">
                <div class="col-6"><strong class="d-block">{{ number_format($profile?->rating_avg ?? 0, 1) }}</strong><span class="text-muted">Rating</span></div>
                <div class="col-6"><strong class="d-block">{{ $profile?->experience_years ?? 0 }}y</strong><span class="text-muted">Experience</span></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('#teacherProfileForm').addEventListener('submit', function () {
    var form = this;
    var input = document.getElementById('skillsInput');
    var values = input.value.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
    input.removeAttribute('name');
    values.forEach(function (skill) {
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'skills[]';
        hidden.value = skill;
        form.appendChild(hidden);
    });
});
</script>
@endpush
