@extends('layouts.frontend')

@section('title', 'Checkout')

@section('content')
<header class="section-padding pb-0" style="background: var(--gradient-hero); padding-top:60px;">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="section-title">Checkout</h1>
    </div>
</header>

<section class="section-padding pt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
                    @csrf
                    @if(count($batchOptions))
                    <div class="filter-card">
                        <h5 class="mb-1">Select Your Batch</h5>
                        <p class="small text-muted mb-4">These courses/books have upcoming batches. Choose the one that fits your schedule.</p>
                        @foreach($items as $item)
                            @continue(! isset($batchOptions[$item->id]))
                            <div class="mb-3">
                                <label class="form-label-custom">{{ $item->purchasable->title }}</label>
                                <select name="batches[{{ $item->id }}]" class="form-select form-control-custom" required>
                                    <option value="">Select a batch</option>
                                    @foreach($batchOptions[$item->id] as $batch)
                                        <option value="{{ $batch->id }}">
                                            {{ $batch->batch_name }} ({{ $batch->batchLevel?->name ?? 'No level' }}) &mdash;
                                            {{ implode(', ', $batch->batch_days ?? []) }},
                                            {{ $batch->class_start_time?->format('g:i A') }}-{{ $batch->class_end_time?->format('g:i A') }}
                                            &mdash; {{ $batch->student_limit - $batch->enrolled_count }} seats left
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="filter-card">
                        <h5 class="mb-4">Billing Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6"><x-form.input name="billing_name" label="Full Name" :value="$user->name" required /></div>
                            <div class="col-md-6"><x-form.input name="billing_email" type="email" label="Email Address" :value="$user->email" required /></div>
                            <div class="col-md-6"><x-form.input name="billing_phone" type="tel" label="Phone Number" :value="$user->phone" required /></div>
                            <div class="col-md-6"><x-form.input name="country" label="Country" :value="$user->country" required /></div>
                            <div class="col-md-4"><x-form.input name="division" label="Division / State" /></div>
                            <div class="col-md-4"><x-form.input name="district" label="District / City" /></div>
                            <div class="col-md-4"><x-form.input name="thana" label="Thana / Area" /></div>
                            <div class="col-12"><x-form.input name="address" label="Street Address" :value="$user->address" required /></div>
                            <div class="col-md-6"><x-form.input name="zip" label="Zip Code" /></div>
                            <div class="col-12"><x-form.textarea name="order_notes" label="Order Notes (optional)" rows="3" /></div>
                        </div>
                    </div>

                    <div class="filter-card mb-0">
                        <h5 class="mb-4">Payment Method</h5>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center gap-2 border rounded-3 p-3">
                                <input type="radio" name="payment_method" value="cod" class="form-check-input" checked> <i class="bi bi-cash-coin"></i> Cash on Delivery
                            </label>
                            <label class="d-flex align-items-center gap-2 border rounded-3 p-3">
                                <input type="radio" name="payment_method" value="sslcommerz" class="form-check-input"> <i class="bi bi-credit-card"></i> SSLCommerz (Card / Mobile Banking)
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-5">
                <div class="filter-card">
                    <h5 class="mb-4">Order Summary</h5>
                    @foreach($items as $item)
                        <div class="d-flex justify-content-between mb-3"><span class="text-muted">{{ $item->purchasable->title }}</span><span>{{ money($item->line_total) }}</span></div>
                    @endforeach
                    <hr>

                    @if($couponError)
                        <div class="alert alert-warning small py-2"><i class="bi bi-exclamation-triangle me-1"></i> {{ $couponError }}</div>
                    @endif

                    {{-- Coupon apply/remove are their own forms — they must NOT be nested
                         inside #checkoutForm, since HTML doesn't allow forms inside forms. --}}
                    <div class="mb-3">
                        <label class="form-label-custom">Coupon Code</label>
                        @if($appliedCoupon)
                            <div class="d-flex align-items-center justify-content-between border rounded-3 p-2 ps-3" style="border-color: var(--success) !important; background: #F0FDF4;">
                                <span class="small fw-semibold text-success"><i class="bi bi-check-circle-fill me-1"></i> {{ $appliedCoupon }} applied</span>
                                <form action="{{ route('checkout.coupon.remove') }}" method="POST" class="mb-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger p-0 small text-decoration-underline border-0 bg-transparent">Remove</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('checkout.coupon') }}" method="POST" class="input-group">
                                @csrf
                                <input type="text" name="coupon_code" class="form-control form-control-custom" placeholder="Enter coupon code">
                                <button class="btn btn-outline-brand" type="submit">Apply</button>
                            </form>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span>{{ money($subtotal) }}</span></div>
                    @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success"><span>Discount</span><span>&minus;{{ money($discount) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between mb-3 pt-3 border-top fw-bold fs-5"><span>Total</span><span class="text-primary-brand">{{ money($total) }}</span></div>

                    <button class="btn btn-brand w-100 mb-2" type="submit" form="checkoutForm">Place Order</button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-brand w-100"><i class="bi bi-arrow-left me-1"></i> Back to Cart</a>
                    <p class="small text-muted mt-3 mb-0"><i class="bi bi-shield-check me-1"></i> Your payment information is encrypted and secure.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
