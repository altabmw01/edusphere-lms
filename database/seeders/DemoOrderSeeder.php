<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoOrderSeeder extends Seeder
{
    /**
     * Builds real Order + OrderItem records for a sample of the enrollments
     * and book purchases created in CourseSeeder / BookSeeder, so Order
     * history, invoices, and admin reports have realistic data to show.
     */
    public function run(): void
    {
        $students = User::role(User::ROLE_STUDENT)->get();

        foreach ($students->random(min(20, $students->count())) as $student) {
            $enrollments = CourseEnrollment::where('user_id', $student->id)->whereNull('order_id')->inRandomOrder()->limit(2)->get();
            $bookPurchases = BookPurchase::where('user_id', $student->id)->whereNull('order_id')->inRandomOrder()->limit(1)->get();

            if ($enrollments->isEmpty() && $bookPurchases->isEmpty()) {
                continue;
            }

            DB::transaction(function () use ($student, $enrollments, $bookPurchases) {
                $subtotal = 0;
                $lines = [];

                foreach ($enrollments as $enrollment) {
                    $course = Course::find($enrollment->course_id);
                    $subtotal += $course->final_price;
                    $lines[] = [
                        'purchasable_type' => Course::class,
                        'purchasable_id' => $course->id,
                        'title' => $course->title,
                        'price' => $course->final_price,
                        'quantity' => 1,
                        'line_total' => $course->final_price,
                        'link' => $enrollment,
                    ];
                }

                foreach ($bookPurchases as $bookPurchase) {
                    $book = Book::find($bookPurchase->book_id);
                    $subtotal += $book->final_price;
                    $lines[] = [
                        'purchasable_type' => Book::class,
                        'purchasable_id' => $book->id,
                        'title' => $book->title,
                        'price' => $book->final_price,
                        'quantity' => 1,
                        'line_total' => $book->final_price,
                        'link' => $bookPurchase,
                    ];
                }

                $paidAt = now()->subDays(fake()->numberBetween(1, 90));

                $order = Order::create([
                    'user_id' => $student->id,
                    'billing_name' => $student->name,
                    'billing_email' => $student->email,
                    'billing_phone' => $student->phone,
                    'country' => $student->country ?? 'United States',
                    'address' => $student->address,
                    'subtotal' => round($subtotal, 2),
                    'discount_total' => 0,
                    'tax_total' => 0,
                    'shipping_total' => 0,
                    'grand_total' => round($subtotal, 2),
                    'payment_method' => fake()->randomElement(['cod', 'sslcommerz']),
                    'payment_status' => 'paid',
                    'status' => 'completed',
                    'paid_at' => $paidAt,
                    'created_at' => $paidAt,
                    'updated_at' => $paidAt,
                ]);

                foreach ($lines as $line) {
                    $order->items()->create([
                        'purchasable_type' => $line['purchasable_type'],
                        'purchasable_id' => $line['purchasable_id'],
                        'title' => $line['title'],
                        'price' => $line['price'],
                        'quantity' => $line['quantity'],
                        'line_total' => $line['line_total'],
                    ]);

                    $line['link']->update(['order_id' => $order->id]);
                }
            });
        }
    }
}
