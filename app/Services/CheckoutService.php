<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected OrderRepositoryInterface $orders,
    ) {
    }

    /**
     * Place an order from the current cart contents.
     *
     * @param array<int,int> $batchSelections Cart item ID => chosen Batch ID (only for
     *        items whose course/book currently offers selectable upcoming batches).
     *
     * @throws \RuntimeException when the cart is empty or a batch selection is invalid
     */
    public function placeOrder(User $user, array $billing, string $paymentMethod, ?string $couponCode = null, array $batchSelections = []): Order
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            throw new \RuntimeException('Your cart is empty.');
        }

        $resolvedBatches = $this->resolveBatchSelections($items, $batchSelections);

        $subtotal = round($items->sum(fn ($item) => $item->line_total), 2);

        $coupon = null;
        $discount = 0;

        if ($couponCode) {
            $result = $this->couponService->validate($couponCode, $items, $subtotal, $user);
            $coupon = $result['coupon'];
            $discount = $result['discount'];
        }

        $tax = round(($subtotal - $discount) * (float) config('lms.tax_percent', 0) / 100, 2);
        $shipping = 0.00; // Digital products only — no physical shipping cost.
        $grandTotal = round($subtotal - $discount + $tax + $shipping, 2);

        return DB::transaction(function () use ($user, $billing, $paymentMethod, $items, $subtotal, $discount, $tax, $shipping, $grandTotal, $coupon, $resolvedBatches) {
            $order = $this->orders->create([
                ...$billing,
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'tax_total' => $tax,
                'shipping_total' => $shipping,
                'grand_total' => $grandTotal,
                'coupon_id' => $coupon?->id,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'purchasable_type' => $item->purchasable_type,
                    'purchasable_id' => $item->purchasable_id,
                    'batch_id' => $resolvedBatches[$item->id] ?? null,
                    'title' => $item->purchasable->title,
                    'price' => $item->purchasable->final_price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ]);
            }

            if ($coupon) {
                $this->couponService->recordUsage($coupon, $user, $order->id, $discount);
            }

            // For Cash on Delivery, mark paid immediately upon placing (adjust per business rules).
            if ($paymentMethod === 'cod') {
                $this->fulfillOrder($order->fresh('items'));
            }

            $this->cartService->clear();

            $user->notify(new OrderPlacedNotification($order));

            return $order->fresh('items');
        });
    }

    /**
     * Validate each cart item's chosen batch (if any) actually belongs to that
     * item's course/book and still has an open seat. Returns cart_item_id => batch_id.
     *
     * @throws \RuntimeException on any invalid selection
     */
    protected function resolveBatchSelections($items, array $batchSelections): array
    {
        $resolved = [];

        foreach ($items as $item) {
            $batchId = $batchSelections[$item->id] ?? null;

            if (! $batchId) {
                continue;
            }

            $batch = Batch::selectableAtCheckout()
                ->where('id', $batchId)
                ->where('batchable_type', $item->purchasable_type)
                ->where('batchable_id', $item->purchasable_id)
                ->first();

            if (! $batch) {
                throw new \RuntimeException('The batch you selected for "' . $item->purchasable->title . '" is no longer available. Please choose another.');
            }

            if (! $batch->has_seats_available) {
                throw new \RuntimeException('The batch you selected for "' . $item->purchasable->title . '" is now full. Please choose another.');
            }

            $resolved[$item->id] = $batch->id;
        }

        return $resolved;
    }

    /**
     * Grant course enrollments / book purchase access and mark the order fulfilled.
     * Called on COD placement, or by the payment gateway callback for online payments.
     * Always reads from the order's own items (which already carry the chosen batch_id)
     * rather than the cart, since the cart may be long gone by the time an async
     * payment gateway callback fires.
     */
    public function fulfillOrder(Order $order): void
    {
        $items = $order->items()->with('purchasable')->get();

        foreach ($items as $item) {
            if ($item->purchasable_type === Course::class) {
                $enrollment = CourseEnrollment::firstOrNew([
                    'user_id' => $order->user_id,
                    'course_id' => $item->purchasable_id,
                ]);
                $enrollment->order_id = $order->id;
                // Only overwrite an existing batch assignment if this order actually
                // picked one — avoids silently clearing a batch an admin assigned
                // manually (e.g. via Assign Students) on a repeat/renewal purchase.
                if ($item->batch_id) {
                    $enrollment->batch_id = $item->batch_id;
                }
                $enrollment->save();

                Course::whereKey($item->purchasable_id)->increment('students_count');
                Course::whereKey($item->purchasable_id)->increment('sales_count');
            }

            if ($item->purchasable_type === Book::class) {
                $purchase = BookPurchase::firstOrNew([
                    'user_id' => $order->user_id,
                    'book_id' => $item->purchasable_id,
                ]);
                $purchase->order_id = $order->id;
                if ($item->batch_id) {
                    $purchase->batch_id = $item->batch_id;
                }
                $purchase->save();

                Book::whereKey($item->purchasable_id)->increment('sales_count');
            }
        }

        $order->update([
            'status' => 'completed',
            'payment_status' => $order->payment_method === 'cod' ? $order->payment_status : 'paid',
            'paid_at' => $order->paid_at ?? now(),
        ]);
    }

    public function markPaidAndFulfill(Order $order, string $transactionId): void
    {
        $order->update(['transaction_id' => $transactionId]);
        $this->fulfillOrder($order);
    }
}
