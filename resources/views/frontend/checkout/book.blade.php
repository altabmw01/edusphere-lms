@extends('layouts.frontend')

@section('title', 'Checkout')

@section('content')
@php($item = $items->first())
@php($unitPrice = (float) $item->purchasable->final_price)

<header class="section-padding pb-0" style="background: var(--gradient-hero); padding-top:60px;">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="section-title">Checkout</h1>
    </div>
</header>

<section class="section-padding pt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <form method="POST" action="{{ route('checkout.store.book') }}" id="checkoutForm">
                    @csrf

                    <div class="filter-card">
                        <h5 class="mb-4">{{ $item->purchasable->title }}</h5>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $item->purchasable->cover_url }}" width="64" height="86" class="rounded-3" style="object-fit:cover;" alt="{{ $item->purchasable->title }}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="price-current" id="unitPriceDisplay">{{ money($unitPrice) }}</span>
                                    <span class="text-muted small">each</span>
                                </div>
                                <label class="form-label-custom mb-1">Quantity</label>
                                <div class="d-flex align-items-center border rounded-pill" style="width:130px;">
                                    <button type="button" class="btn btn-sm px-3" id="qtyMinus">&minus;</button>
                                    <input type="number" name="quantity" id="qtyInput" value="{{ $quantity }}" min="1" max="20" class="form-control border-0 text-center p-0" style="width:40px;" readonly>
                                    <button type="button" class="btn btn-sm px-3" id="qtyPlus">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($batchOptions))
                    <div class="filter-card">
                        <h5 class="mb-1">Select Your Batch</h5>
                        <p class="small text-muted mb-4">This book has upcoming reading group batches. Choose one, or skip if not applicable.</p>
                        <select name="batches[{{ $item->id }}]" class="form-select form-control-custom">
                            <option value="">No batch</option>
                            @foreach($batchOptions[$item->id] as $batch)
                                <option value="{{ $batch->id }}">
                                    {{ $batch->batch_name }} &mdash; {{ implode(', ', $batch->batch_days ?? []) }},
                                    {{ $batch->class_start_time?->format('g:i A') }}-{{ $batch->class_end_time?->format('g:i A') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="filter-card">
                        <h5 class="mb-4">Billing Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6"><x-form.input name="billing_name" label="Full Name" :value="$user->name" required /></div>
                            <div class="col-md-6"><x-form.input name="billing_email" type="email" label="Email Address" :value="$user->email" required /></div>
                            <div class="col-md-6"><x-form.input name="billing_phone" type="tel" label="Phone Number" :value="$user->phone" required /></div>
                        </div>
                    </div>

                    

                    <div class="filter-card">
                        <h5 class="mb-4">Shipping Address</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="countrySelect" class="form-select form-control-custom" required>
                                    <option value="">Select country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" data-code="{{ $country->country_code }}" @selected($country->isBangladesh())>{{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6"><x-form.input name="zip" label="Zip / Postal Code" /></div>

                            <div class="col-md-6 bd-field d-none">
                                <label class="form-label-custom">Division <span class="text-danger">*</span></label>
                                <select name="division_id" id="divisionSelect" class="form-select form-control-custom">
                                    <option value="">Select division</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 bd-field d-none">
                                <label class="form-label-custom">District <span class="text-danger">*</span></label>
                                <select name="district_id" id="districtSelect" class="form-select form-control-custom">
                                    <option value="">Select district</option>
                                </select>
                            </div>
                            <div class="col-md-6 bd-field d-none">
                                <label class="form-label-custom">Thana <span class="text-danger">*</span></label>
                                <select name="thana_id" id="thanaSelect" class="form-select form-control-custom">
                                    <option value="">Select thana</option>
                                </select>
                            </div>
                            <div class="col-md-6 bd-field d-none">
                                <label class="form-label-custom">Union</label>
                                <select name="union_id" id="unionSelect" class="form-select form-control-custom">
                                    <option value="">Select union (optional)</option>
                                </select>
                            </div>

                            <div class="col-12"><x-form.input name="address" label="Street Address" :value="$user->address" required /></div>
                            <div class="col-12"><x-form.textarea name="order_notes" label="Order Notes (optional)" rows="2" /></div>
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
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">{{ $item->purchasable->title }} &times; <span id="qtyLabel">{{ $quantity }}</span></span>
                        <span id="lineTotalDisplay">{{ money($unitPrice * $quantity) }}</span>
                    </div>
                    <hr>

                    @if($couponError)
                        <div class="alert alert-warning small py-2"><i class="bi bi-exclamation-triangle me-1"></i> {{ $couponError }}</div>
                    @endif

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

                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span id="subtotalDisplay">{{ money($unitPrice * $quantity) }}</span></div>
                    @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success"><span>Discount</span><span>&minus;{{ money($discount) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Shipping</span><span id="shippingDisplay">{{ money(0) }}</span></div>
                    <p class="small text-muted mb-2" id="shippingHint">Select a country to calculate shipping.</p>
                    <div class="d-flex justify-content-between mb-3 pt-3 border-top fw-bold fs-5"><span>Total</span><span class="text-primary-brand" id="totalDisplay">{{ money($total) }}</span></div>

                    <button class="btn btn-brand w-100 mb-2" type="submit" form="checkoutForm">Place Order</button>
                    <a href="{{ route('books.index') }}" class="btn btn-outline-brand w-100"><i class="bi bi-arrow-left me-1"></i> Continue Shopping</a>
                    <p class="small text-muted mt-3 mb-0"><i class="bi bi-shield-check me-1"></i> Your payment information is encrypted and secure. Shipping is estimated here and confirmed on the order confirmation page.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    const unitPrice = {{ $unitPrice }};
    const discountAmount = {{ $discount }};
    const currencySymbol = @json(config('lms.currency_symbol'));
    const countryShippingMap = @json($countryShippingMap);
    const shippingCostDhaka = {{ $shippingCostDhaka }};
    const shippingCostOutsideDhaka = {{ $shippingCostOutsideDhaka }};

    const qtyInput = document.getElementById('qtyInput');
    const qtyLabel = document.getElementById('qtyLabel');
    const lineTotalDisplay = document.getElementById('lineTotalDisplay');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const shippingDisplay = document.getElementById('shippingDisplay');
    const shippingHint = document.getElementById('shippingHint');
    const totalDisplay = document.getElementById('totalDisplay');

    const countrySelect = document.getElementById('countrySelect');
    const divisionSelect = document.getElementById('divisionSelect');
    const districtSelect = document.getElementById('districtSelect');
    const thanaSelect = document.getElementById('thanaSelect');
    const unionSelect = document.getElementById('unionSelect');
    const bdFields = document.querySelectorAll('.bd-field');

    let currentShipping = 0;

    function formatMoney(amount) {
        return currencySymbol + amount.toFixed(2);
    }

    function recalcTotals() {
        const qty = parseInt(qtyInput.value, 10) || 1;
        const subtotal = unitPrice * qty;
        const total = subtotal - discountAmount + currentShipping;

        qtyLabel.textContent = qty;
        lineTotalDisplay.textContent = formatMoney(subtotal);
        subtotalDisplay.textContent = formatMoney(subtotal);
        shippingDisplay.textContent = formatMoney(currentShipping);
        totalDisplay.textContent = formatMoney(Math.max(total, 0));
    }

    document.getElementById('qtyMinus').addEventListener('click', function () {
        qtyInput.value = Math.max(1, (parseInt(qtyInput.value, 10) || 1) - 1);
        recalcTotals();
    });
    document.getElementById('qtyPlus').addEventListener('click', function () {
        qtyInput.value = Math.min(20, (parseInt(qtyInput.value, 10) || 1) + 1);
        recalcTotals();
    });

    function resetSelect(select, placeholder) {
        select.innerHTML = '<option value="">' + placeholder + '</option>';
    }

    function populateSelect(select, items, placeholder) {
        resetSelect(select, placeholder);
        items.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            select.appendChild(opt);
        });
    }

    function isBangladeshSelected() {
        const opt = countrySelect.options[countrySelect.selectedIndex];
        return opt && opt.dataset.code === 'BD';
    }

    function updateShippingForNonBangladesh() {
        const countryId = countrySelect.value;
        currentShipping = countryId && countryShippingMap[countryId] ? parseFloat(countryShippingMap[countryId]) : 0;
        shippingHint.textContent = countryId ? '' : 'Select a country to calculate shipping.';
        recalcTotals();
    }

    countrySelect.addEventListener('change', function () {
        if (isBangladeshSelected()) {
            bdFields.forEach(el => el.classList.remove('d-none'));
            resetSelect(districtSelect, 'Select district');
            resetSelect(thanaSelect, 'Select thana');
            resetSelect(unionSelect, 'Select union (optional)');
            currentShipping = shippingCostOutsideDhaka; // default until a district is chosen
            shippingHint.textContent = 'Select your division, district, and thana below.';
            recalcTotals();
        } else {
            bdFields.forEach(el => el.classList.add('d-none'));
            updateShippingForNonBangladesh();
        }
    });

    divisionSelect?.addEventListener('change', function () {
        resetSelect(districtSelect, 'Select district');
        resetSelect(thanaSelect, 'Select thana');
        resetSelect(unionSelect, 'Select union (optional)');
        if (!this.value) return;

        fetch('/locations/divisions/' + this.value + '/districts')
            .then(r => r.json())
            .then(data => populateSelect(districtSelect, data, 'Select district'));
    });

    districtSelect?.addEventListener('change', function () {
        resetSelect(thanaSelect, 'Select thana');
        resetSelect(unionSelect, 'Select union (optional)');

        const selectedText = this.options[this.selectedIndex] ? this.options[this.selectedIndex].textContent.trim().toLowerCase() : '';
        currentShipping = selectedText === 'dhaka' ? shippingCostDhaka : shippingCostOutsideDhaka;
        shippingHint.textContent = '';
        recalcTotals();

        if (!this.value) return;

        fetch('/locations/districts/' + this.value + '/thanas')
            .then(r => r.json())
            .then(data => populateSelect(thanaSelect, data, 'Select thana'));
    });

    thanaSelect?.addEventListener('change', function () {
        resetSelect(unionSelect, 'Select union (optional)');
        if (!this.value) return;

        fetch('/locations/thanas/' + this.value + '/unions')
            .then(r => r.json())
            .then(data => populateSelect(unionSelect, data, 'Select union (optional)'));
    });

    // Bangladesh is pre-selected by default (matches the currency/market this
    // store is configured for) — trigger the initial cascade state on load.
    if (isBangladeshSelected()) {
        bdFields.forEach(el => el.classList.remove('d-none'));
        currentShipping = shippingCostOutsideDhaka;
        shippingHint.textContent = 'Select your division, district, and thana below.';
    } else {
        updateShippingForNonBangladesh();
    }

    recalcTotals();
})();
</script>
@endpush
