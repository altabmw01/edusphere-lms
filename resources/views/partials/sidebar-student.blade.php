@php($r = fn($name) => request()->routeIs($name) ? 'active' : '')

<nav class="nav flex-column pb-4">
    <span class="nav-section-title">Overview</span>
    <a class="nav-link {{ $r('student.dashboard') }}" href="{{ route('student.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <span class="nav-section-title">Learning</span>
    <a class="nav-link {{ $r('student.my-courses.*') }}" href="{{ route('student.my-courses.index') }}"><i class="bi bi-collection-play"></i> My Courses</a>
    <a class="nav-link {{ $r('student.my-books.*') }}" href="{{ route('student.my-books.index') }}"><i class="bi bi-journal-bookmark"></i> My Books</a>
    <a class="nav-link {{ $r('student.certificates.*') }}" href="{{ route('student.certificates.index') }}"><i class="bi bi-patch-check"></i> Certificates</a>
    <a class="nav-link {{ $r('student.wishlist.*') }}" href="{{ route('student.wishlist.index') }}"><i class="bi bi-heart"></i> Wishlist</a>

    <span class="nav-section-title">Account</span>
    <a class="nav-link {{ $r('student.orders.*') }}" href="{{ route('student.orders.index') }}"><i class="bi bi-receipt"></i> Order History</a>
    <a class="nav-link {{ $r('profile.edit') }}" href="{{ route('profile.edit') }}"><i class="bi bi-gear"></i> Account Settings</a>
    <a class="nav-link" href="{{ route('courses.index') }}"><i class="bi bi-search"></i> Browse Courses</a>
</nav>
