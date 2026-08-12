<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'type' => fake()->randomElement(['course', 'book']),
            'icon' => 'bi-' . fake()->randomElement(['code-slash', 'palette', 'megaphone', 'briefcase', 'book', 'cpu']),
            'color' => fake()->randomElement(['#2563EB', '#0EA5E9', '#F59E0B', '#22C55E', '#EF4444', '#8B5CF6']),
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 20),
            'status' => true,
        ];
    }
}
