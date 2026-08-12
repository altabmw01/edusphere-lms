@extends('layouts.app')

@section('title', 'My Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-primary);"><i class="bi bi-collection-play"></i></div><h4 class="mb-0">{{ $coursesInProgress + $coursesCompleted }}</h4><small class="text-muted">Purchased Courses</small></div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-warm);"><i class="bi bi-journal-bookmark"></i></div><h4 class="mb-0">{{ $bookPurchases->count() }}</h4><small class="text-muted">Purchased Books</small></div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="stat-card"><div class="stat-icon" style="background:#22C55E;"><i class="bi bi-patch-check"></i></div><h4 class="mb-0">{{ $certificatesCount }}</h4><small class="text-muted">Certificates</small></div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="stat-card"><div class="stat-icon" style="background:#8B5CF6;"><i class="bi bi-heart"></i></div><h4 class="mb-0">{{ $wishlistCount }}</h4><small class="text-muted">Wishlist</small></div>
    </div>
</div>

<div class="filter-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">Continue Learning</h6>
        <a href="{{ route('student.my-courses.index') }}" class="small text-primary-brand">View all</a>
    </div>
    <div class="row g-3">
        @forelse($enrollments as $enrollment)
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3 border rounded-3 p-2">
                    <img src="{{ $enrollment->course->thumbnail_url }}" width="80" height="60" class="rounded-3" style="object-fit:cover;" alt="{{ $enrollment->course->title }}">
                    <div class="flex-grow-1">
                        <span class="d-block small fw-semibold">{{ \Illuminate\Support\Str::limit($enrollment->course->title, 34) }}</span>
                        <div class="progress progress-thin my-1"><div class="progress-bar" style="width:{{ $enrollment->progress_percent }}%;"></div></div>
                        <small class="text-muted">{{ $enrollment->progress_percent }}% complete</small>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted small mb-0">You haven't enrolled in any courses yet. <a href="{{ route('courses.index') }}">Browse courses</a></p>
        @endforelse
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="table-brand">
            <div class="p-3 border-bottom"><h6 class="mb-0">Recent Orders</h6></div>
            <table class="table mb-0">
                <thead><tr><th>Order</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('student.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ money($order->grand_total) }}</td>
                            <td><x-status-badge :status="$order->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-brand">
            <div class="p-3 border-bottom"><h6 class="mb-0">My Books</h6></div>
            @forelse($bookPurchases as $purchase)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <img src="{{ $purchase->book->cover_url }}" width="42" height="56" class="rounded-2" style="object-fit:cover;" alt="{{ $purchase->book->title }}">
                    <span class="small flex-grow-1">{{ \Illuminate\Support\Str::limit($purchase->book->title, 30) }}</span>
                    <a href="{{ route('student.my-books.download', $purchase->book) }}" class="btn btn-icon-circle"><i class="bi bi-download"></i></a>
                </div>
            @empty
                <p class="text-muted small p-3 mb-0">No books purchased yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
