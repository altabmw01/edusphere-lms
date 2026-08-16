<?php

namespace Tests\Feature\Commerce;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Course checkout — digital product, no quantity, no shipping address.
 * Book checkout (quantity + shipping) is covered separately in BookCheckoutTest.
 */
class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function validBillingPayload(): array
    {
        return [
            'billing_name' => 'Jane Learner',
            'billing_email' => 'jane@example.com',
            'billing_phone' => '+15550001111',
            'payment_method' => 'cod',
        ];
    }

    public function test_checkout_redirects_to_browse_courses_when_empty(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)->get('/checkout')->assertRedirect(route('courses.index'));
    }

    public function test_placing_a_cash_on_delivery_order_creates_the_order_and_enrolls_the_student(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create(['price' => 100, 'discount_price' => null]);

        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);

        $response = $this->actingAs($user)->post('/checkout/course', $this->validBillingPayload());

        $order = $user->fresh()->orders()->first();

        $this->assertNotNull($order);
        $this->assertSame('completed', $order->status);
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame('100.00', number_format($order->grand_total, 2));
        $this->assertSame('0.00', number_format($order->shipping_total, 2));
        $response->assertRedirect(route('student.orders.show', $order->order_number));

        $this->assertTrue(
            CourseEnrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()
        );

        // Cart should be emptied after a successful order.
        $this->assertSame(0, $user->fresh()->cartItems()->count());
    }

    public function test_checkout_fails_gracefully_if_the_cart_is_empty(): void
    {
        $user = User::factory()->student()->create();

        $response = $this->actingAs($user)->post('/checkout/course', $this->validBillingPayload());

        // No cart items exist for this user, so nothing to validate against —
        // the request still passes basic field validation but placeOrder()
        // throws on an empty cart, redirecting back to checkout.
        $response->assertRedirect(route('checkout.index'));
        $this->assertSame(0, $user->orders()->count());
    }

    public function test_checkout_requires_billing_fields(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create();
        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);

        $response = $this->actingAs($user)->post('/checkout/course', ['payment_method' => 'cod']);

        $response->assertSessionHasErrors(['billing_name', 'billing_email', 'billing_phone']);
    }

    public function test_selecting_a_valid_upcoming_batch_assigns_the_student_to_it(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create(['price' => 100, 'discount_price' => null]);
        $batch = \App\Models\Batch::factory()->create([
            'batchable_type' => Course::class,
            'batchable_id' => $course->id,
            'status' => true,
            'hide_batch' => false,
            'upcoming_status' => true,
            'student_limit' => 10,
        ]);

        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);
        $cartItemId = $user->cartItems()->first()->id;

        $response = $this->actingAs($user)->post('/checkout/course', [
            ...$this->validBillingPayload(),
            'batches' => [$cartItemId => $batch->id],
        ]);

        $order = $user->fresh()->orders()->first();
        $response->assertRedirect(route('student.orders.show', $order->order_number));

        $this->assertSame($batch->id, $order->items()->first()->batch_id);

        $enrollment = CourseEnrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        $this->assertSame($batch->id, $enrollment->batch_id);
    }

    public function test_checkout_rejects_a_batch_that_does_not_belong_to_the_purchased_course(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create(['price' => 100]);
        $unrelatedCourse = Course::factory()->create();
        $batch = \App\Models\Batch::factory()->create([
            'batchable_type' => Course::class,
            'batchable_id' => $unrelatedCourse->id,
            'status' => true,
            'hide_batch' => false,
            'upcoming_status' => true,
        ]);

        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);
        $cartItemId = $user->cartItems()->first()->id;

        $response = $this->actingAs($user)->post('/checkout/course', [
            ...$this->validBillingPayload(),
            'batches' => [$cartItemId => $batch->id],
        ]);

        $response->assertRedirect(route('checkout.index'));
        $this->assertSame(0, $user->orders()->count());
    }

    public function test_checkout_rejects_a_full_batch(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create(['price' => 100]);
        $batch = \App\Models\Batch::factory()->create([
            'batchable_type' => Course::class,
            'batchable_id' => $course->id,
            'status' => true,
            'hide_batch' => false,
            'upcoming_status' => true,
            'student_limit' => 1,
        ]);

        // Fill the batch's only seat with a different student first.
        CourseEnrollment::create([
            'user_id' => User::factory()->student()->create()->id,
            'course_id' => $course->id,
            'batch_id' => $batch->id,
        ]);

        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);
        $cartItemId = $user->cartItems()->first()->id;

        $response = $this->actingAs($user)->post('/checkout/course', [
            ...$this->validBillingPayload(),
            'batches' => [$cartItemId => $batch->id],
        ]);

        $response->assertRedirect(route('checkout.index'));
        $this->assertSame(0, $user->orders()->count());
    }
}
