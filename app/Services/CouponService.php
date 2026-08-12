<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Collection;

class CouponService
{
    /**
     * Validate a coupon code against the current cart and user, returning
     * the coupon and calculated discount, or a validation error message.
     *
     * @return array{coupon: ?Coupon, discount: float, error: ?string}
     */
    public function validate(string $code, Collection $cartItems, float $subtotal, User $user): array
    {
        $coupon = Coupon::where('code', $code)->active()->first();

        if (! $coupon) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Invalid or expired coupon code.'];
        }

        if ($coupon->isExpired()) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'This coupon has expired.'];
        }

        if ($coupon->hasReachedLimit()) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'This coupon has reached its usage limit.'];
        }

        $userUsageCount = $coupon->usages()->where('user_id', $user->id)->count();
        if ($userUsageCount >= $coupon->per_user_limit) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'You have already used this coupon.'];
        }

        if ($subtotal < $coupon->minimum_purchase) {
            return ['coupon' => null, 'discount' => 0, 'error' => "Minimum purchase of " . money($coupon->minimum_purchase) . " required."];
        }

        if ($coupon->applicable_to !== 'all') {
            $requiredType = $coupon->applicable_to === 'courses' ? 'App\\Models\\Course' : 'App\\Models\\Book';
            $applicableSubtotal = $cartItems
                ->filter(fn ($item) => $item->purchasable_type === $requiredType)
                ->sum(fn ($item) => $item->line_total);

            if ($applicableSubtotal <= 0) {
                return ['coupon' => null, 'discount' => 0, 'error' => 'This coupon does not apply to items in your cart.'];
            }

            $subtotal = $applicableSubtotal;
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return ['coupon' => $coupon, 'discount' => $discount, 'error' => null];
    }

    public function recordUsage(Coupon $coupon, User $user, int $orderId, float $discountAmount): void
    {
        $coupon->usages()->create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
        ]);

        $coupon->increment('used_count');
    }
}
