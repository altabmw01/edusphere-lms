<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\PlaceOrderRequest;
use App\Models\Batch;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\CouponService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CheckoutService $checkoutService,
        protected CouponService $couponService,
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $subtotal = $this->cartService->subtotal();
        $discount = 0;
        $couponError = null;

        if ($code = session('applied_coupon')) {
            $result = $this->couponService->validate($code, $items, $subtotal, auth()->user());

            if ($result['error']) {
                // The coupon that was applied earlier no longer qualifies (cart
                // changed, expired, limit reached, etc.) — drop it silently and
                // let the customer know, rather than pretending it still applies.
                session()->forget('applied_coupon');
                $couponError = $result['error'];
            } else {
                $discount = $result['discount'];
            }
        }

        return view('frontend.checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => round($subtotal - $discount, 2),
            'appliedCoupon' => $discount > 0 ? session('applied_coupon') : null,
            'couponError' => $couponError,
            'user' => auth()->user(),
            'batchOptions' => $this->batchOptionsFor($items),
        ]);
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate(['coupon_code' => ['required', 'string']]);

        $items = $this->cartService->items();
        $result = $this->couponService->validate(
            $request->string('coupon_code'),
            $items,
            $this->cartService->subtotal(),
            auth()->user(),
        );

        if ($result['error']) {
            return back()->withErrors(['coupon_code' => $result['error']]);
        }

        session(['applied_coupon' => $request->string('coupon_code')->toString()]);

        return back()->with('status', "Coupon applied — you saved " . money($result['discount']) . ".");
    }

    public function removeCoupon(): RedirectResponse
    {
        session()->forget('applied_coupon');

        return back()->with('status', 'Coupon removed.');
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->checkoutService->placeOrder(
                user: $request->user(),
                billing: $request->safe()->except(['payment_method', 'coupon_code', 'batches']),
                paymentMethod: $request->validated('payment_method'),
                couponCode: $request->validated('coupon_code') ?? session('applied_coupon'),
                batchSelections: array_filter($request->validated('batches') ?? []),
            );
        } catch (\RuntimeException $e) {
            return redirect()->route('checkout.index')->with('error', $e->getMessage());
        }

        session()->forget('applied_coupon');

        if ($order->payment_method === 'sslcommerz') {
            // Redirect to the SSLCommerz gateway integration entry point.
            return redirect()->route('payment.sslcommerz.init', $order->order_number);
        }

        return redirect()->route('student.orders.show', $order->order_number)
            ->with('status', 'Your order has been placed successfully!');
    }

    /**
     * For each cart item, look up any batches the student can pick at checkout
     * (active, visible, marked upcoming, with an open seat), keyed by cart item ID.
     *
     * @return array<int, \Illuminate\Support\Collection<int, Batch>>
     */
    protected function batchOptionsFor($items): array
    {
        $options = [];

        foreach ($items as $item) {
            $batches = Batch::selectableAtCheckout()
                ->where('batchable_type', $item->purchasable_type)
                ->where('batchable_id', $item->purchasable_id)
                ->with('batchLevel')
                ->get()
                ->filter(fn (Batch $batch) => $batch->has_seats_available)
                ->values();

            if ($batches->isNotEmpty()) {
                $options[$item->id] = $batches;
            }
        }

        return $options;
    }
}
