<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->randomElement([29, 39, 49, 59, 69, 79, 89, 99, 119, 149]);
        $hasDiscount = fake()->boolean(70);

        return [
            'title' => ucfirst(fake()->words(4, true)),
            'category_id' => Category::factory()->state(['type' => 'course']),
            'created_by' => User::factory()->admin(),
            'price' => $price,
            'discount_price' => $hasDiscount ? round($price * fake()->randomFloat(2, 0.55, 0.85)) : null,
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced', 'all_levels']),
            'language' => 'English',
            'duration_minutes' => fake()->numberBetween(180, 3000),
            'lessons_count' => fake()->numberBetween(20, 220),
            'has_certificate' => true,
            'description' => fake()->paragraphs(3, true),
            'requirements' => implode("\n", fake()->sentences(3)),
            'target_audience' => implode("\n", fake()->sentences(2)),
            'what_you_will_learn' => implode("\n", fake()->sentences(6)),
            'status' => 'published',
            'is_featured' => fake()->boolean(30),
            'is_trending' => fake()->boolean(20),
            'meta_title' => null,
            'meta_description' => null,
            'published_at' => now()->subDays(fake()->numberBetween(1, 400)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft', 'published_at' => null]);
    }

    /**
     * rating_avg / rating_count / students_count / sales_count are intentionally
     * excluded from $fillable (they're system-maintained aggregates, never set
     * via a form). The factory fills realistic demo values with a raw update
     * after creation so mass-assignment protection stays intact everywhere else.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\Course $course) {
            $course->newQuery()->whereKey($course->id)->update([
                'rating_avg' => fake()->randomFloat(2, 3.8, 5.0),
                'rating_count' => fake()->numberBetween(10, 3500),
                'students_count' => fake()->numberBetween(50, 12000),
                'sales_count' => fake()->numberBetween(50, 12000),
            ]);
        });
    }
}
