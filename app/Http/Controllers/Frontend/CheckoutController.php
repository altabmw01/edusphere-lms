<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\PlaceBookOrderRequest;
use App\Http\Requests\Checkout\PlaceCourseOrderRequest;
use App\Models\Batch;
use App\Models\Book;
use App\Models\Country;
use App\Models\District;
use App\Models\Division;
use App\Models\Thana;
use App\Models\Union;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\CouponService;
use App\Services\ShippingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CheckoutService $checkoutService,
        protected CouponService $couponService,
        protected ShippingService $shippingService,
    ) {
    }

    /**
     * This project sells one course/book at a time, and the two flows are
     * different enough (quantity + physical shipping address for books;
     * neither for courses) that they get entirely separate checkout pages.
     * Which one renders is decided from the cart's own contents — never
     * from a client-supplied "type" — so it can't be spoofed.
     */
    public function index(): View|RedirectResponse
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            return redirect()->route('courses.index')->with('status', 'Your cart is empty — add a course or book to get started.');
        }

        $isBook = $items->first()->purchasable_type === Book::class;
        $items = $items->filter(fn ($item) => ($item->purchasable_type === Book::class) === $isBook)->values();

        $subtotal = round($items->sum(fn ($item) => $item->line_total), 2);
        [$discount, $appliedCoupon, $couponError] = $this->resolveAppliedCoupon($items, $subtotal);

        $common = [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'appliedCoupon' => $appliedCoupon,
            'couponError' => $couponError,
            'user' => auth()->user(),
            'batchOptions' => $this->batchOptionsFor($items),
        ];

        if ($isBook) {
            $item = $items->first();
            $quantity = $item->quantity;
            $shipping = 0.0; // unknown until a country is chosen client-side; recalculated on submit
            $total = round($subtotal - $discount + $shipping, 2);

            return view('frontend.checkout.book', [
                ...$common,
                'quantity' => $quantity,
                'shipping' => $shipping,
                'total' => $total,
                'countries' => Country::orderBy('country_name')->get(),
                'divisions' => \App\Models\Division::orderBy('name')->get(),
                'countryShippingMap' => Country::pluck('shipping_cost', 'id'),
                'shippingCostDhaka' => (float) setting('shipping_cost_dhaka', 0),
                'shippingCostOutsideDhaka' => (float) setting('shipping_cost_outside_dhaka', 0),
            ]);
        }

        $total = round($subtotal - $discount, 2);

        return view('frontend.checkout.course', [...$common, 'total' => $total]);
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

    public function storeCourse(PlaceCourseOrderRequest $request): RedirectResponse
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

        return $this->afterOrderPlaced($order);
    }

    public function storeBook(PlaceBookOrderRequest $request): RedirectResponse
    {
        $items = $this->cartService->items()->filter(fn ($item) => $item->purchasable_type === Book::class);

        if ($items->isEmpty()) {
            return redirect()->route('checkout.index')->with('error', 'Your cart is empty.');
        }

        // Quantity applies to the single book being purchased. Updating the
        // cart item's own quantity here means every downstream calculation
        // (subtotal, order_item.quantity, order_item.line_total) just works
        // through the existing cart→order pipeline unchanged.
        $this->cartService->updateQuantity($items->first()->id, (int) $request->validated('quantity'));

        $country = Country::find($request->validated('country_id'));
        $division = $request->filled('division_id') ? Division::find($request->validated('division_id')) : null;
        $district = $request->filled('district_id') ? District::find($request->validated('district_id')) : null;
        $thana = $request->filled('thana_id') ? Thana::find($request->validated('thana_id')) : null;
        $union = $request->filled('union_id') ? Union::find($request->validated('union_id')) : null;

        $shippingCost = $this->shippingService->calculate($country, $district);

        $billing = [
            'billing_name' => $request->validated('billing_name'),
            'billing_email' => $request->validated('billing_email'),
            'billing_phone' => $request->validated('billing_phone'),
            'address' => $request->validated('address'),
            'zip' => $request->validated('zip'),
            'order_notes' => $request->validated('order_notes'),
            'country' => $country?->country_name,
            'division' => $division?->name,
            'district' => $district?->name,
            'thana' => $thana?->name,
            'union' => $union?->name,
        ];

        try {
            $order = $this->checkoutService->placeOrder(
                user: $request->user(),
                billing: $billing,
                paymentMethod: $request->validated('payment_method'),
                couponCode: $request->validated('coupon_code') ?? session('applied_coupon'),
                batchSelections: array_filter($request->validated('batches') ?? []),
                shippingCost: $shippingCost,
            );
        } catch (\RuntimeException $e) {
            return redirect()->route('checkout.index')->with('error', $e->getMessage());
        }

        return $this->afterOrderPlaced($order);
    }

    protected function afterOrderPlaced($order): RedirectResponse
    {
        session()->forget('applied_coupon');

        if ($order->payment_method === 'sslcommerz') {
            return redirect()->route('payment.sslcommerz.init', $order->order_number);
        }

        return redirect()->route('student.orders.show', $order->order_number)
            ->with('status', 'Your order has been placed successfully!');
    }

    protected function resolveAppliedCoupon($items, float $subtotal): array
    {
        $discount = 0;
        $appliedCoupon = null;
        $couponError = null;

        if ($code = session('applied_coupon')) {
            $result = $this->couponService->validate($code, $items, $subtotal, auth()->user());

            if ($result['error']) {
                session()->forget('applied_coupon');
                $couponError = $result['error'];
            } else {
                $discount = $result['discount'];
                $appliedCoupon = $code;
            }
        }

        return [$discount, $appliedCoupon, $couponError];
    }

    /**
     * For each cart item, look up any batches the student can pick at checkout
     * (active, visible, marked upcoming, with an open seat), keyed by cart item ID.
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
