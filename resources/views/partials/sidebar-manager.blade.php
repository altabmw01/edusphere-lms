@php($r = fn($name) => request()->routeIs($name) ? 'active' : '')

<nav class="nav flex-column pb-4">
    <span class="nav-section-title">Overview</span>
    <a class="nav-link {{ $r('manager.dashboard') }}" href="{{ route('manager.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <span class="nav-section-title">Catalog</span>
    <a class="nav-link {{ $r('manager.books.*') }}" href="{{ route('manager.books.index') }}"><i class="bi bi-journal-bookmark"></i> Books</a>
    <a class="nav-link {{ $r('manager.categories.*') }}" href="{{ route('manager.categories.index') }}"><i class="bi bi-tags"></i> Categories</a>

    <span class="nav-section-title">Commerce</span>
    <a class="nav-link {{ $r('manager.orders.*') }}" href="{{ route('manager.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a>
    <a class="nav-link {{ $r('manager.coupons.*') }}" href="{{ route('manager.coupons.index') }}"><i class="bi bi-ticket-perforated"></i> Coupons</a>

    <span class="nav-section-title">People</span>
    <a class="nav-link {{ $r('manager.users.*') }}" href="{{ route('manager.users.index') }}"><i class="bi bi-people"></i> Users</a>
    <a class="nav-link {{ $r('manager.reviews.*') }}" href="{{ route('manager.reviews.index') }}"><i class="bi bi-star"></i> Reviews</a>

    <a class="nav-link mt-3" href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
</nav>
