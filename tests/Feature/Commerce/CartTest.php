<?php

namespace Tests\Feature\Commerce;

use App\Models\Book;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_clicking_add_to_cart_is_redirected_to_the_identify_flow_instead_of_adding_directly(): void
    {
        $course = Course::factory()->create();

        $response = $this->post('/cart', ['type' => 'course', 'id' => $course->id]);

        $response->assertRedirect(route('purchase.identify'));
        $this->assertSame(['type' => 'course', 'id' => $course->id], session('intended_purchase'));

        // Nothing was actually added to any cart yet.
        $this->get('/cart')->assertDontSee($course->title);
    }

    public function test_a_guest_adding_a_book_is_also_redirected_to_identify(): void
    {
        $book = Book::factory()->create();

        $this->post('/cart', ['type' => 'book', 'id' => $book->id])
            ->assertRedirect(route('purchase.identify'));

        $this->assertSame(['type' => 'book', 'id' => $book->id], session('intended_purchase'));
    }

    public function test_cart_rejects_an_invalid_purchasable_type(): void
    {
        $response = $this->post('/cart', ['type' => 'invalid', 'id' => 1]);

        $response->assertSessionHasErrors('type');
    }

    public function test_an_authenticated_user_is_sent_straight_to_checkout_after_adding_an_item(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);

        $response->assertRedirect(route('checkout.index'));
        $this->assertTrue($user->cartItems()->where('purchasable_id', $course->id)->exists());
    }

    public function test_an_authenticated_user_can_remove_an_item_from_their_cart(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create();

        $this->actingAs($user)->post('/cart', ['type' => 'course', 'id' => $course->id]);
        $this->actingAs($user)->get('/cart')->assertSee($course->title);

        $cartItemId = $user->cartItems()->first()->id;

        $this->actingAs($user)->delete("/cart/{$cartItemId}");

        $this->actingAs($user)->get('/cart')->assertDontSee($course->title);
    }
}
