@php($r = fn($name) => request()->routeIs($name) ? 'active' : '')

<nav class="nav flex-column pb-4">
    <span class="nav-section-title">Overview</span>
    <a class="nav-link {{ $r('admin.dashboard') }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <span class="nav-section-title">Catalog</span>
    <a class="nav-link {{ $r('admin.courses.*') }}" href="{{ route('admin.courses.index') }}"><i class="bi bi-collection-play"></i> Courses</a>
    <a class="nav-link {{ $r('admin.books.*') }}" href="{{ route('admin.books.index') }}"><i class="bi bi-journal-bookmark"></i> Books</a>
    <a class="nav-link {{ $r('admin.categories.*') }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
	<a class="nav-link {{ $r('admin.batches.*') }}" href="{{ route('admin.batches.index') }}"><i class="bi bi-calendar3"></i> Batches</a>

    <span class="nav-section-title">Commerce</span>
    <a class="nav-link {{ $r('admin.orders.*') }}" href="{{ route('admin.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a>
    <a class="nav-link {{ $r('admin.coupons.*') }}" href="{{ route('admin.coupons.index') }}"><i class="bi bi-ticket-perforated"></i> Coupons</a>
    <a class="nav-link {{ $r('admin.reports.*') }}" href="{{ route('admin.reports.index') }}"><i class="bi bi-graph-up"></i> Reports</a>

    <span class="nav-section-title">People</span>
    <a class="nav-link {{ $r('admin.users.*') }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Users</a>
    <a class="nav-link {{ $r('admin.reviews.*') }}" href="{{ route('admin.reviews.index') }}"><i class="bi bi-star"></i> Reviews</a>

    <span class="nav-section-title">Content</span>
    <a class="nav-link {{ $r('admin.faqs.*') }}" href="{{ route('admin.faqs.index') }}"><i class="bi bi-question-circle"></i> FAQs</a>
    <a class="nav-link {{ $r('admin.testimonials.*') }}" href="{{ route('admin.testimonials.index') }}"><i class="bi bi-chat-quote"></i> Testimonials</a>
    <a class="nav-link {{ $r('admin.messages.*') }}" href="{{ route('admin.messages.index') }}"><i class="bi bi-envelope"></i> Contact Messages</a>

    <span class="nav-section-title">System</span>
    <a class="nav-link {{ $r('admin.activity-log.*') }}" href="{{ route('admin.activity-log.index') }}"><i class="bi bi-clock-history"></i> Activity Log</a>
    <a class="nav-link {{ $r('admin.settings.*') }}" href="{{ route('admin.settings.edit') }}"><i class="bi bi-gear"></i> Settings</a>
    <a class="nav-link" href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
</nav>
