<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 300);

        return [
            'user_id' => User::factory()->student(),
            'billing_name' => fake()->name(),
            'billing_email' => fake()->safeEmail(),
            'billing_phone' => fake()->numerify('+1 555 ### ####'),
            'country' => fake()->country(),
            'address' => fake()->streetAddress(),
            'subtotal' => $subtotal,
            'discount_total' => 0,
            'tax_total' => 0,
            'shipping_total' => 0,
            'grand_total' => $subtotal,
            'payment_method' => fake()->randomElement(['cod', 'sslcommerz']),
            'payment_status' => 'pending',
            'status' => 'pending',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }
}
