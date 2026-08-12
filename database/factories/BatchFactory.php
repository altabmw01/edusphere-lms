<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Batch>
 */
class BatchFactory extends Factory
{
    public function definition(): array
    {
        $days = fake()->randomElements(['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'], fake()->numberBetween(2, 3));
        $startedDate = now()->addDays(fake()->numberBetween(1, 14));

        return [
            'batchable_type' => Course::class,
            'batchable_id' => Course::factory(),
            'teacher_id' => User::factory()->teacher(),
            'batch_level_id' => null,
            'batch_number' => 'B-' . fake()->unique()->numerify('####'),
            'batch_name' => 'Batch ' . fake()->numerify('##'),
            'class_start_time' => '19:00:00',
            'class_end_time' => '21:00:00',
            'batch_days' => $days,
            'weekly_days' => count($days),
            'batch_started_date' => $startedDate,
            'batch_end_date' => $startedDate->copy()->addMonths(3),
            'student_limit' => 30,
            'free_or_paid' => true,
            'upcoming_status' => true,
            'hide_batch' => false,
            'added_by' => null,
            'updated_by' => null,
            'status' => true,
        ];
    }

    public function forBook(): static
    {
        return $this->state(fn (array $attributes) => [
            'batchable_type' => \App\Models\Book::class,
            'batchable_id' => \App\Models\Book::factory(),
        ]);
    }

    public function full(): static
    {
        return $this->state(fn (array $attributes) => ['student_limit' => 0]);
    }
}
