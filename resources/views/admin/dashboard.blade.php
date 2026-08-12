@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-primary);"><i class="bi bi-cash-stack"></i></div><h4 class="mb-0">{{ money($stats['revenue']) }}</h4><small class="text-muted">Total Revenue</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-warm);"><i class="bi bi-receipt"></i></div><h4 class="mb-0">{{ number_format($stats['orders']) }}</h4><small class="text-muted">Total Orders</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#22C55E;"><i class="bi bi-collection-play"></i></div><h4 class="mb-0">{{ number_format($stats['courses_sold']) }}</h4><small class="text-muted">Courses Sold</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#8B5CF6;"><i class="bi bi-journal-bookmark"></i></div><h4 class="mb-0">{{ number_format($stats['books_sold']) }}</h4><small class="text-muted">Books Sold</small></div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-primary);"><i class="bi bi-people"></i></div><h4 class="mb-0">{{ number_format($stats['students']) }}</h4><small class="text-muted">Students</small></div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#0EA5E9;"><i class="bi bi-person-video3"></i></div><h4 class="mb-0">{{ number_format($stats['teachers']) }}</h4><small class="text-muted">Teachers</small></div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#F59E0B;"><i class="bi bi-person-badge"></i></div><h4 class="mb-0">{{ number_format($stats['managers']) }}</h4><small class="text-muted">Managers</small></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="filter-card mb-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Monthly Sales</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-sm-pill btn-outline-brand"><i class="bi bi-plus"></i> New Course</a>
                    <a href="{{ route('admin.books.create') }}" class="btn btn-sm-pill btn-brand"><i class="bi bi-plus"></i> New Book</a>
                </div>
            </div>
            <canvas id="salesChart" height="110"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="filter-card mb-3">
            <h6 class="mb-3">Top Selling Courses</h6>
            @foreach($topCourses as $course)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small">{{ \Illuminate\Support\Str::limit($course->title, 28) }}</span>
                    <span class="badge bg-brand-light text-primary-brand">{{ $course->sales_count }} sold</span>
                </div>
            @endforeach
        </div>
        <div class="filter-card mb-0">
            <h6 class="mb-3">Top Selling Books</h6>
            @foreach($topBooks as $book)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small">{{ \Illuminate\Support\Str::limit($book->title, 28) }}</span>
                    <span class="badge bg-brand-light text-primary-brand">{{ $book->sales_count }} sold</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-brand">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0">Recent Orders</h6>
                <a href="{{ route('admin.orders.index') }}" class="small text-primary-brand">View all</a>
            </div>
            <table class="table mb-0">
                <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
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
        <div class="table-brand mb-3">
            <div class="p-3 border-bottom"><h6 class="mb-0">Latest Users</h6></div>
            @foreach($latestUsers as $user)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <img src="{{ $user->avatarUrl() }}" class="avatar-sm" alt="{{ $user->name }}">
                    <div class="flex-grow-1"><span class="d-block small fw-semibold">{{ $user->name }}</span><span class="text-muted small text-capitalize">{{ $user->role }}</span></div>
                    <span class="text-muted small">{{ $user->created_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: @json($monthlySales['labels']),
        datasets: [{
            label: 'Revenue',
            data: @json($monthlySales['data']),
            borderColor: '#2563EB',
            backgroundColor: 'rgba(37,99,235,0.1)',
            fill: true,
            tension: 0.35,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
