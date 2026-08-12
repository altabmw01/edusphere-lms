@extends('layouts.app')

@section('title', $viewedUser->name)
@section('page-title', 'User Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="filter-card text-center">
            <img src="{{ $viewedUser->avatarUrl() }}" class="avatar-upload mb-3" alt="{{ $viewedUser->name }}">
            <h5 class="mb-0">{{ $viewedUser->name }}</h5>
            <span class="text-muted text-capitalize">{{ $viewedUser->role }}</span>
            <hr>
            <div class="text-start small text-muted">
                <p class="mb-1"><i class="bi bi-envelope me-2"></i>{{ $viewedUser->email }}</p>
                <p class="mb-1"><i class="bi bi-telephone me-2"></i>{{ $viewedUser->phone ?? '—' }}</p>
                <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>{{ $viewedUser->address ?? '—' }}</p>
                <p class="mb-0"><i class="bi bi-calendar me-2"></i>Joined {{ $viewedUser->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="table-brand mb-4">
            <div class="p-3 border-bottom"><h6 class="mb-0">Orders</h6></div>
            <table class="table mb-0">
                <thead><tr><th>Order</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($viewedUser->orders as $order)
                        <tr><td>{{ $order->order_number }}</td><td>{{ money($order->grand_total) }}</td><td><x-status-badge :status="$order->status" /></td></tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($viewedUser->role === 'student')
        <div class="table-brand">
            <div class="p-3 border-bottom"><h6 class="mb-0">Enrolled Courses</h6></div>
            <table class="table mb-0">
                <thead><tr><th>Course</th><th>Progress</th></tr></thead>
                <tbody>
                    @forelse($viewedUser->enrollments as $enrollment)
                        <tr><td>{{ $enrollment->course->title }}</td><td>{{ $enrollment->progress_percent }}%</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-4">No enrollments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
