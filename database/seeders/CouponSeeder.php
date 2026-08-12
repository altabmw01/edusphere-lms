<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role(User::ROLE_ADMIN)->first();

        Coupon::create([
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'minimum_purchase' => 0,
            'maximum_discount' => 20,
            'usage_limit' => 1000,
            'per_user_limit' => 1,
            'used_count' => 0,
            'applicable_to' => 'all',
            'expires_at' => now()->addYear(),
            'status' => true,
            'created_by' => $admin->id,
        ]);

        Coupon::create([
            'code' => 'SAVE20',
            'type' => 'fixed',
            'value' => 20,
            'minimum_purchase' => 50,
            'maximum_discount' => null,
            'usage_limit' => 200,
            'per_user_limit' => 1,
            'used_count' => 0,
            'applicable_to' => 'courses',
            'expires_at' => now()->addMonths(3),
            'status' => true,
            'created_by' => $admin->id,
        ]);

        Coupon::create([
            'code' => 'BOOKLOVER',
            'type' => 'percentage',
            'value' => 15,
            'minimum_purchase' => 15,
            'maximum_discount' => 15,
            'usage_limit' => null,
            'per_user_limit' => 2,
            'used_count' => 0,
            'applicable_to' => 'books',
            'expires_at' => now()->addMonths(6),
            'status' => true,
            'created_by' => $admin->id,
        ]);

        Coupon::factory()->count(5)->create();
    }
}
