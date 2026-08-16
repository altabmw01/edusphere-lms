@php($r = fn($name) => request()->routeIs($name) ? 'active' : '')

<nav class="nav flex-column pb-4">
    <span class="nav-section-title">Overview</span>
    <a class="nav-link {{ $r('teacher.dashboard') }}" href="{{ route('teacher.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <span class="nav-section-title">Teaching</span>
    <a class="nav-link {{ $r('teacher.batches.*') }}" href="{{ route('teacher.batches.index') }}"><i class="bi bi-calendar3"></i> My Batches</a>
    <a class="nav-link {{ $r('teacher.students.*') }}" href="{{ route('teacher.students.index') }}"><i class="bi bi-people"></i> My Students</a>

    <span class="nav-section-title">Account</span>
    <a class="nav-link {{ $r('teacher.profile.*') }}" href="{{ route('teacher.profile.edit') }}"><i class="bi bi-person"></i> Teacher Profile</a>
    <a class="nav-link {{ $r('profile.edit') }}" href="{{ route('profile.edit') }}"><i class="bi bi-gear"></i> Account Settings</a>
    <a class="nav-link" href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
</nav>
