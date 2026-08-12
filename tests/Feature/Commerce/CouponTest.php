<?php

namespace Tests\Feature\Commerce;

use App\Models\Coupon;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_percentage_coupon_reduces_the_order_total(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create(['price' => 100, 'discount_price' => null]);
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
            'maximum_discount' => 50,
            'minimum_purchase' => 0,
            'status' => true,
            'expires_at' => now()->addMonth(),
        ]);

        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);

        $response = $this->actingAs($user)->post('/checkout', [
            'billing_name' => 'Jane Learner',
            'billing_email' => 'jane@example.com',
            'billing_phone' => '+15550001111',
            'country' => 'United States',
            'address' => '123 Main St',
            'payment_method' => 'cod',
            'coupon_code' => $coupon->code,
        ]);

        $order = $user->fresh()->orders()->first();

        $response->assertRedirect(route('student.orders.show', $order->order_number));
        $this->assertSame('10.00', number_format($order->discount_total, 2));
        $this->assertSame('90.00', number_format($order->grand_total, 2));
        $this->assertSame(1, $coupon->fresh()->used_count);
    }

    public function test_an_expired_coupon_is_rejected_and_order_placed_at_full_price(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create(['price' => 100, 'discount_price' => null]);
        $coupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'status' => true,
            'expires_at' => now()->subDay(),
        ]);

        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);

        $order = null;
        $this->actingAs($user)->post('/checkout', [
            'billing_name' => 'Jane Learner',
            'billing_email' => 'jane@example.com',
            'billing_phone' => '+15550001111',
            'country' => 'United States',
            'address' => '123 Main St',
            'payment_method' => 'cod',
            'coupon_code' => $coupon->code,
        ]);

        $order = $user->fresh()->orders()->first();

        // Expired coupon silently fails validation inside CheckoutService (discount = 0),
        // so the order is still placed but at full price with no coupon attached.
        $this->assertSame('0.00', number_format($order->discount_total, 2));
        $this->assertNull($order->coupon_id);
        $this->assertSame(0, $coupon->fresh()->used_count);
    }

    public function test_a_coupon_cannot_be_used_more_than_its_per_user_limit(): void
    {
        $user = User::factory()->student()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'ONEUSE',
            'per_user_limit' => 1,
            'status' => true,
            'expires_at' => now()->addMonth(),
        ]);

        $priorOrder = \App\Models\Order::factory()->create(['user_id' => $user->id]);
        $coupon->usages()->create(['user_id' => $user->id, 'order_id' => $priorOrder->id, 'discount_amount' => 5]);

        $course = Course::factory()->create(['price' => 100]);
        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);

        $this->actingAs($user)->post('/checkout', [
            'billing_name' => 'Jane Learner',
            'billing_email' => 'jane@example.com',
            'billing_phone' => '+15550001111',
            'country' => 'United States',
            'address' => '123 Main St',
            'payment_method' => 'cod',
            'coupon_code' => $coupon->code,
        ]);

        $order = $user->fresh()->orders()->latest()->first();
        $this->assertSame('0.00', number_format($order->discount_total, 2));
    }
}
