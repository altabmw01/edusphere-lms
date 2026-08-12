<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('+1 555 ### ####'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_STUDENT,
            'bio' => fake()->optional()->sentence(12),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_ADMIN]);
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_MANAGER]);
    }

    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_TEACHER]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_STUDENT]);
    }
}
