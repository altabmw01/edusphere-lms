<?php

namespace Tests\Feature\Commerce;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseIdentifyFlowTest extends TestCase
{
    use RefreshDatabase;

    private function startIntendedPurchase(Course $course): void
    {
        $this->post('/cart', ['type' => 'course', 'id' => $course->id]);
    }

    public function test_visiting_identify_without_an_intended_item_redirects_home(): void
    {
        $this->get('/purchase/identify')->assertRedirect(route('home'));
    }

    public function test_an_unrecognized_phone_number_routes_to_registration(): void
    {
        $course = Course::factory()->create();
        $this->startIntendedPurchase($course);

        $response = $this->post('/purchase/identify', ['phone' => '+15559998888']);

        $response->assertRedirect(route('purchase.register'));
        $this->assertSame('+15559998888', session('identify_phone'));
    }

    public function test_a_known_phone_number_routes_to_the_password_step(): void
    {
        $existing = User::factory()->student()->create(['phone' => '+15551234567']);
        $course = Course::factory()->create();
        $this->startIntendedPurchase($course);

        $this->post('/purchase/identify', ['phone' => '+15551234567'])
            ->assertRedirect(route('purchase.password'));

        $this->get('/purchase/password')->assertOk()->assertSee($existing->name);
    }

    public function test_correct_password_logs_in_adds_the_item_and_redirects_to_checkout(): void
    {
        $existing = User::factory()->student()->create([
            'phone' => '+15551234567',
            'password' => bcrypt('Password123!'),
        ]);
        $course = Course::factory()->create(['price' => 75]);
        $this->startIntendedPurchase($course);
        $this->post('/purchase/identify', ['phone' => '+15551234567']);

        $response = $this->post('/purchase/password', ['password' => 'Password123!']);

        $this->assertAuthenticatedAs($existing);
        $response->assertRedirect(route('checkout.index'));
        $this->assertTrue($existing->fresh()->cartItems()->where('purchasable_id', $course->id)->exists());
        $this->assertNull(session('intended_purchase'));
    }

    public function test_incorrect_password_is_rejected_and_does_not_log_in(): void
    {
        User::factory()->student()->create([
            'phone' => '+15551234567',
            'password' => bcrypt('Password123!'),
        ]);
        $course = Course::factory()->create();
        $this->startIntendedPurchase($course);
        $this->post('/purchase/identify', ['phone' => '+15551234567']);

        $response = $this->post('/purchase/password', ['password' => 'wrong-password']);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registering_a_new_account_creates_the_user_logs_in_adds_the_item_and_redirects_to_checkout(): void
    {
        $course = Course::factory()->create(['price' => 60]);
        $this->startIntendedPurchase($course);
        $this->post('/purchase/identify', ['phone' => '+15557778888']);

        $response = $this->post('/purchase/register', [
            'name' => 'New Buyer',
            'email' => 'newbuyer@example.com',
            'phone' => '+15557778888',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'newbuyer@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame(User::ROLE_STUDENT, $user->role);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('checkout.index'));
        $this->assertTrue($user->cartItems()->where('purchasable_id', $course->id)->exists());
    }

    public function test_registration_rejects_a_phone_number_already_in_use(): void
    {
        User::factory()->student()->create(['phone' => '+15551112222']);
        $course = Course::factory()->create();
        $this->startIntendedPurchase($course);

        $response = $this->post('/purchase/register', [
            'name' => 'Someone Else',
            'email' => 'someone@example.com',
            'phone' => '+15551112222',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    public function test_an_already_authenticated_user_cannot_access_the_identify_flow(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)->get('/purchase/identify')->assertRedirect();
    }
}
