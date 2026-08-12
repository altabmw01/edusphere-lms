@extends('layouts.app')

@section('title', 'Order History')
@section('page-title', 'Order History')

@section('content')
<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Order</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ money($order->grand_total) }}</td>
                    <td><x-status-badge :status="$order->payment_status" /></td>
                    <td><x-status-badge :status="$order->status" /></td>
                    <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="text-end"><a href="{{ route('student.orders.show', $order) }}" class="btn btn-icon-circle"><i class="bi bi-eye"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No orders yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
