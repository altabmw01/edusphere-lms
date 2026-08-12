@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<form method="GET" class="filter-card d-flex flex-wrap gap-3 align-items-end">
    <div>
        <label class="form-label-custom">From</label>
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-custom">
    </div>
    <div>
        <label class="form-label-custom">To</label>
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-custom">
    </div>
    <button class="btn btn-brand" type="submit">Apply</button>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('admin.reports.orders.excel') }}" class="btn btn-outline-brand"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</a>
        <a href="{{ route('admin.reports.orders.pdf') }}" class="btn btn-outline-brand"><i class="bi bi-file-earmark-pdf me-1"></i> Export PDF</a>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-primary);"><i class="bi bi-cash-stack"></i></div><h4 class="mb-0">{{ money($totalRevenue) }}</h4><small class="text-muted">Revenue ({{ $from->format('M d') }} – {{ $to->format('M d') }})</small></div>
    </div>
    <div class="col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-warm);"><i class="bi bi-receipt"></i></div><h4 class="mb-0">{{ $totalOrders }}</h4><small class="text-muted">Orders in Range</small></div>
    </div>
</div>

<div class="filter-card">
    <h6 class="mb-3">Monthly Sales (12 months)</h6>
    <canvas id="salesChart" height="90"></canvas>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="table-brand">
            <div class="p-3 border-bottom"><h6 class="mb-0">Top Selling Courses</h6></div>
            <table class="table mb-0">
                <thead><tr><th>Course</th><th>Sold</th></tr></thead>
                <tbody>
                    @foreach($topCourses as $course)
                        <tr><td>{{ \Illuminate\Support\Str::limit($course->title, 40) }}</td><td>{{ $course->sales_count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-brand">
            <div class="p-3 border-bottom"><h6 class="mb-0">Top Selling Books</h6></div>
            <table class="table mb-0">
                <thead><tr><th>Book</th><th>Sold</th></tr></thead>
                <tbody>
                    @foreach($topBooks as $book)
                        <tr><td>{{ \Illuminate\Support\Str::limit($book->title, 40) }}</td><td>{{ $book->sales_count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: @json($monthlySales['labels']),
        datasets: [{ label: 'Revenue', data: @json($monthlySales['data']), backgroundColor: '#2563EB' }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
