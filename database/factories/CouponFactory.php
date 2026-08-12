<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['percentage', 'fixed']);

        return [
            'code' => strtoupper(fake()->unique()->bothify('SAVE##??')),
            'type' => $type,
            'value' => $type === 'percentage' ? fake()->numberBetween(10, 40) : fake()->numberBetween(5, 30),
            'minimum_purchase' => fake()->randomElement([0, 20, 50]),
            'maximum_discount' => $type === 'percentage' ? fake()->numberBetween(20, 60) : null,
            'usage_limit' => fake()->numberBetween(50, 500),
            'per_user_limit' => 1,
            'used_count' => 0,
            'applicable_to' => 'all',
            'expires_at' => now()->addMonths(fake()->numberBetween(1, 6)),
            'status' => true,
            'created_by' => User::factory()->admin(),
        ];
    }
}
