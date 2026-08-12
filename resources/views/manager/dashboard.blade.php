@extends('layouts.app')

@section('title', 'Manager Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-primary);"><i class="bi bi-cash-stack"></i></div><h4 class="mb-0">{{ money($revenueThisMonth) }}</h4><small class="text-muted">Revenue This Month</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-warm);"><i class="bi bi-hourglass-split"></i></div><h4 class="mb-0">{{ $pendingOrders }}</h4><small class="text-muted">Pending Orders</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#22C55E;"><i class="bi bi-ticket-perforated"></i></div><h4 class="mb-0">{{ $activeCoupons }}</h4><small class="text-muted">Active Coupons</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#8B5CF6;"><i class="bi bi-star"></i></div><h4 class="mb-0">{{ $pendingReviews }}</h4><small class="text-muted">Pending Reviews</small></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-brand">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0">Recent Orders</h6>
                <a href="{{ route('manager.orders.index') }}" class="small text-primary-brand">View all</a>
            </div>
            <table class="table mb-0">
                <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('manager.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ money($order->grand_total) }}</td>
                            <td><x-status-badge :status="$order->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="table-brand">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0">Pending Reviews</h6>
                <a href="{{ route('manager.reviews.index') }}" class="small text-primary-brand">Moderate</a>
            </div>
            @forelse($pendingReviewsList as $review)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <img src="{{ $review->user->avatarUrl() }}" class="avatar-sm" alt="{{ $review->user->name }}">
                    <div class="flex-grow-1">
                        <span class="d-block small fw-semibold">{{ $review->user->name }}</span>
                        <span class="rating-stars small">{!! star_rating($review->rating) !!}</span>
                    </div>
                </div>
            @empty
                <p class="text-muted small p-3 mb-0">No pending reviews.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-md-3 col-6"><a href="{{ route('manager.categories.index') }}" class="text-decoration-none"><div class="feature-card text-center"><i class="bi bi-tags fs-3 text-primary-brand mb-2 d-block"></i><span>Categories</span></div></a></div>
    <div class="col-md-3 col-6"><a href="{{ route('manager.coupons.index') }}" class="text-decoration-none"><div class="feature-card text-center"><i class="bi bi-ticket-perforated fs-3 text-primary-brand mb-2 d-block"></i><span>Coupons</span></div></a></div>
    <div class="col-md-3 col-6"><a href="{{ route('manager.books.index') }}" class="text-decoration-none"><div class="feature-card text-center"><i class="bi bi-journal-bookmark fs-3 text-primary-brand mb-2 d-block"></i><span>Books</span></div></a></div>
    <div class="col-md-3 col-6"><a href="{{ route('manager.users.index') }}" class="text-decoration-none"><div class="feature-card text-center"><i class="bi bi-people fs-3 text-primary-brand mb-2 d-block"></i><span>Users</span></div></a></div>
</div>
@endsection
