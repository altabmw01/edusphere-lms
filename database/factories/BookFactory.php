<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->randomElement([14, 19, 22, 24, 26, 28, 30, 34, 36]);
        $hasDiscount = fake()->boolean(60);

        return [
            'title' => ucfirst(fake()->words(3, true)),
            'author' => fake()->name(),
            'category_id' => Category::factory()->state(['type' => 'book']),
            'added_by' => User::factory()->admin(),
            'description' => fake()->paragraphs(2, true),
            'price' => $price,
            'discount_price' => $hasDiscount ? round($price * fake()->randomFloat(2, 0.7, 0.9)) : null,
            'pages' => fake()->numberBetween(120, 480),
            'language' => 'English',
            'publisher' => fake()->company(),
            'edition' => fake()->randomElement(['1st Edition', '2nd Edition', '3rd Edition']),
            'isbn' => fake()->isbn13(),
            'is_featured' => fake()->boolean(30),
            'status' => 'published',
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    /**
     * rating_avg / rating_count / sales_count / downloads_count are system-maintained
     * aggregates, deliberately left out of $fillable. Seed realistic demo values here.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\Book $book) {
            $book->newQuery()->whereKey($book->id)->update([
                'rating_avg' => fake()->randomFloat(2, 3.8, 5.0),
                'rating_count' => fake()->numberBetween(5, 900),
                'sales_count' => fake()->numberBetween(20, 4000),
                'downloads_count' => fake()->numberBetween(20, 4000),
            ]);
        });
    }
}
