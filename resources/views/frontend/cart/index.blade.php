@extends('layouts.frontend')

@section('title', 'Your Cart')

@section('content')
<header class="section-padding pb-0" style="background: var(--gradient-hero); padding-top:60px;">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="section-title">Your Cart</h1>
    </div>
</header>

<section class="section-padding pt-5">
    <div class="container">
        @if($items->isEmpty())
            <div class="empty-state">
                <i class="bi bi-cart-x"></i>
                <p>Your cart is empty.</p>
                <a href="{{ route('courses.index') }}" class="btn btn-brand">Browse Courses</a>
            </div>
        @else
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="filter-card">
                        <h6 class="mb-3">{{ $items->count() }} Item(s) in Cart</h6>
                        @foreach($items as $item)
                            <div class="d-flex align-items-center gap-3 py-3 border-bottom flex-wrap">
                                <img src="{{ $item->purchasable->thumbnail_url ?? $item->purchasable->cover_url }}" class="rounded-3" alt="{{ $item->purchasable->title }}" style="width:100px;height:74px;object-fit:cover;">
                                <div class="flex-grow-1" style="min-width:180px;">
                                    <span class="badge bg-brand-light text-primary-brand mb-1">{{ $item->purchasable_type === \App\Models\Course::class ? 'Course' : 'Book' }}</span>
                                    <h6 class="mb-0">{{ $item->purchasable->title }}</h6>
                                </div>
                                <div style="width:90px;" class="text-end fw-semibold">{{ money($item->line_total) }}</div>
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon-circle" aria-label="Remove item"><i class="bi bi-trash3"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-brand mt-2"><i class="bi bi-arrow-left me-1"></i> Continue Shopping</a>
                </div>
                <div class="col-lg-4">
                    <div class="filter-card">
                        <h6 class="mb-3">Cart Total</h6>
                        <div class="d-flex justify-content-between mb-3 pt-3 border-top fw-bold fs-5"><span>Subtotal</span><span class="text-primary-brand">{{ money($subtotal) }}</span></div>
                        <a href="{{ route('checkout.index') }}" class="btn btn-brand w-100">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
