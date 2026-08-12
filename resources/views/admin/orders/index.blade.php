@extends('layouts.app')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<form method="GET" class="d-flex gap-2 mb-4">
    <select name="status" class="form-select form-control-custom" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        @foreach(['pending', 'processing', 'completed', 'cancelled', 'refunded'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <select name="payment_status" class="form-select form-control-custom" onchange="this.form.submit()">
        <option value="">All Payment Statuses</option>
        @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
            <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</form>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ money($order->grand_total) }}</td>
                    <td><x-status-badge :status="$order->payment_status" /></td>
                    <td><x-status-badge :status="$order->status" /></td>
                    <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="text-end"><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-icon-circle"><i class="bi bi-eye"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
