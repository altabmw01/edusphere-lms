@php($canManage = $canManage ?? false)

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-brand mb-4">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Order Items</h6>
                <a href="{{ $invoiceRoute }}" class="btn btn-outline-brand btn-sm-pill"><i class="bi bi-download me-1"></i> Download Invoice</a>
            </div>
            <table class="table mb-0">
                <thead><tr><th>Item</th><th>Type</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ class_basename($item->purchasable_type) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ money($item->price) }}</td>
                            <td>{{ money($item->line_total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="filter-card mb-0">
            <h6 class="fw-bold mb-3">Billing Information</h6>
            <div class="row g-2 small text-muted">
                <div class="col-md-6"><strong class="text-dark">Name:</strong> {{ $order->billing_name }}</div>
                <div class="col-md-6"><strong class="text-dark">Email:</strong> {{ $order->billing_email }}</div>
                <div class="col-md-6"><strong class="text-dark">Phone:</strong> {{ $order->billing_phone }}</div>
                <div class="col-md-6"><strong class="text-dark">Country:</strong> {{ $order->country }}</div>
                <div class="col-12"><strong class="text-dark">Address:</strong> {{ $order->address }} {{ $order->district }} {{ $order->zip }}</div>
                @if($order->order_notes)<div class="col-12"><strong class="text-dark">Notes:</strong> {{ $order->order_notes }}</div>@endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Order Summary</h6>
            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Order #</span><span>{{ $order->order_number }}</span></div>
            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Date</span><span>{{ $order->created_at->format('M d, Y') }}</span></div>
            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Payment Method</span><span class="text-uppercase">{{ $order->payment_method }}</span></div>
            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Payment Status</span><x-status-badge :status="$order->payment_status" /></div>
            <div class="d-flex justify-content-between small mb-3"><span class="text-muted">Order Status</span><x-status-badge :status="$order->status" /></div>
            <hr>
            <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Discount</span><span>-{{ money($order->discount_total) }}</span></div>
            <div class="d-flex justify-content-between small mb-3"><span>Tax</span><span>{{ money($order->tax_total) }}</span></div>
            <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-3"><span>Total</span><span class="text-primary-brand">{{ money($order->grand_total) }}</span></div>
        </div>

        @if($canManage)
            <div class="filter-card mb-0">
                <h6 class="fw-bold mb-3">Update Order</h6>
                <form method="POST" action="{{ $updateRoute }}">
                    @csrf @method('PUT')
                    <x-form.select name="status" label="Order Status" :options="['pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded']" :value="$order->status" required />
                    <x-form.select name="payment_status" label="Payment Status" :options="['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded']" :value="$order->payment_status" required />
                    <button class="btn btn-brand w-100">Update Order</button>
                </form>
            </div>
        @elseif($order->canBeCancelled())
            <form method="POST" action="{{ route('student.orders.cancel', $order) }}" data-confirm="Cancel this order?">
                @csrf @method('PUT')
                <button class="btn btn-outline-danger w-100">Cancel Order</button>
            </form>
        @endif
    </div>
</div>
