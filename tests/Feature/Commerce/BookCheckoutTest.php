<?php

namespace Tests\Feature\Commerce;

use App\Models\Book;
use App\Models\Country;
use App\Models\District;
use App\Models\Division;
use App\Models\Setting;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::updateOrCreate(['key' => 'shipping_cost_dhaka'], ['value' => 60]);
        Setting::updateOrCreate(['key' => 'shipping_cost_outside_dhaka'], ['value' => 120]);
    }

    private function bangladeshWithDhaka(): array
    {
        $country = Country::create(['country_code' => 'BD', 'country_name' => 'Bangladesh', 'shipping_cost' => 0]);
        $division = Division::create(['name' => 'Dhaka Division']);
        $district = District::create(['division_id' => $division->id, 'name' => 'Dhaka']);
        $thana = Thana::create(['district_id' => $district->id, 'name' => 'Gulshan']);

        return compact('country', 'division', 'district', 'thana');
    }

    private function validBillingPayload(): array
    {
        return [
            'billing_name' => 'Jane Reader',
            'billing_email' => 'jane@example.com',
            'billing_phone' => '+15550001111',
            'address' => '123 Main St',
            'payment_method' => 'cod',
        ];
    }

    public function test_book_checkout_requires_quantity_and_country(): void
    {
        $user = User::factory()->student()->create();
        $book = Book::factory()->create();
        $this->actingAs($user)->post('/cart', ['type' => 'book', 'id' => $book->id]);

        $response = $this->actingAs($user)->post('/checkout/book', $this->validBillingPayload());

        $response->assertSessionHasErrors(['quantity', 'country_id']);
    }

    public function test_bangladesh_requires_division_district_and_thana(): void
    {
        $user = User::factory()->student()->create();
        $book = Book::factory()->create();
        $loc = $this->bangladeshWithDhaka();
        $this->actingAs($user)->post('/cart', ['type' => 'book', 'id' => $book->id]);

        $response = $this->actingAs($user)->post('/checkout/book', [
            ...$this->validBillingPayload(),
            'quantity' => 1,
            'country_id' => $loc['country']->id,
        ]);

        $response->assertSessionHasErrors(['division_id', 'district_id', 'thana_id']);
    }

    public function test_a_non_bangladesh_country_does_not_require_division_or_district(): void
    {
        $user = User::factory()->student()->create();
        $book = Book::factory()->create(['price' => 20]);
        $country = Country::create(['country_code' => 'US', 'country_name' => 'United States', 'shipping_cost' => 15]);
        $this->actingAs($user)->post('/cart', ['type' => 'book', 'id' => $book->id]);

        $response = $this->actingAs($user)->post('/checkout/book', [
            ...$this->validBillingPayload(),
            'quantity' => 1,
            'country_id' => $country->id,
        ]);

        $response->assertSessionDoesntHaveErrors(['division_id', 'district_id', 'thana_id']);

        $order = $user->fresh()->orders()->first();
        $this->assertSame('15.00', number_format($order->shipping_total, 2));
        $this->assertSame('United States', $order->country);
    }

    public function test_quantity_multiplies_the_order_subtotal(): void
    {
        $user = User::factory()->student()->create();
        $book = Book::factory()->create(['price' => 20, 'discount_price' => null]);
        $loc = $this->bangladeshWithDhaka();
        $this->actingAs($user)->post('/cart', ['type' => 'book', 'id' => $book->id]);

        $this->actingAs($user)->post('/checkout/book', [
            ...$this->validBillingPayload(),
            'quantity' => 3,
            'country_id' => $loc['country']->id,
            'division_id' => $loc['division']->id,
            'district_id' => $loc['district']->id,
            'thana_id' => $loc['thana']->id,
        ]);

        $order = $user->fresh()->orders()->first();
        $this->assertSame('60.00', number_format($order->subtotal, 2)); // 20 x 3
        $this->assertSame(3, $order->items()->first()->quantity);
    }

    public function test_dhaka_district_uses_the_dhaka_shipping_rate(): void
    {
        $user = User::factory()->student()->create();
        $book = Book::factory()->create(['price' => 20, 'discount_price' => null]);
        $loc = $this->bangladeshWithDhaka();
        $this->actingAs($user)->post('/cart', ['type' => 'book', 'id' => $book->id]);

        $this->actingAs($user)->post('/checkout/book', [
            ...$this->validBillingPayload(),
            'quantity' => 1,
            'country_id' => $loc['country']->id,
            'division_id' => $loc['division']->id,
            'district_id' => $loc['district']->id,
            'thana_id' => $loc['thana']->id,
        ]);

        $order = $user->fresh()->orders()->first();
        $this->assertSame('60.00', number_format($order->shipping_total, 2));
        $this->assertSame('80.00', number_format($order->grand_total, 2)); // 20 + 60 shipping
        $this->assertSame('Dhaka', $order->district);
    }

    public function test_a_non_dhaka_bangladesh_district_uses_the_outside_dhaka_rate(): void
    {
        $user = User::factory()->student()->create();
        $book = Book::factory()->create(['price' => 20, 'discount_price' => null]);
        $country = Country::create(['country_code' => 'BD', 'country_name' => 'Bangladesh', 'shipping_cost' => 0]);
        $division = Division::create(['name' => 'Chittagong Division']);
        $district = District::create(['division_id' => $division->id, 'name' => 'Chittagong']);
        $thana = Thana::create(['district_id' => $district->id, 'name' => 'Panchlaish']);

        $this->actingAs($user)->post('/cart', ['type' => 'book', 'id' => $book->id]);

        $this->actingAs($user)->post('/checkout/book', [
            ...$this->validBillingPayload(),
            'quantity' => 1,
            'country_id' => $country->id,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'thana_id' => $thana->id,
        ]);

        $order = $user->fresh()->orders()->first();
        $this->assertSame('120.00', number_format($order->shipping_total, 2));
    }

    public function test_union_is_optional_even_within_bangladesh(): void
    {
        $user = User::factory()->student()->create();
        $book = Book::factory()->create();
        $loc = $this->bangladeshWithDhaka();
        $this->actingAs($user)->post('/cart', ['type' => 'book', 'id' => $book->id]);

        $response = $this->actingAs($user)->post('/checkout/book', [
            ...$this->validBillingPayload(),
            'quantity' => 1,
            'country_id' => $loc['country']->id,
            'division_id' => $loc['division']->id,
            'district_id' => $loc['district']->id,
            'thana_id' => $loc['thana']->id,
        ]);

        $response->assertSessionDoesntHaveErrors('union_id');
        $this->assertSame(1, $user->orders()->count());
    }
}
