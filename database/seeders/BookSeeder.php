<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role(User::ROLE_ADMIN)->first();
        $categories = Category::type('book')->get();
        $students = User::role(User::ROLE_STUDENT)->get();

        Book::factory()
            ->count(18)
            ->create()
            ->each(function (Book $book) use ($admin, $categories, $students) {
                $book->update([
                    'added_by' => $admin->id,
                    'category_id' => $categories->random()->id,
                ]);

                $buyers = $students->random(min(6, $students->count()));
                foreach ($buyers as $buyer) {
                    BookPurchase::create([
                        'user_id' => $buyer->id,
                        'book_id' => $book->id,
                        'download_count' => fake()->numberBetween(0, 5),
                        'last_downloaded_at' => fake()->boolean(60) ? now()->subDays(fake()->numberBetween(1, 45)) : null,
                    ]);
                }
            });
    }
}
