<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\BatchClass;
use App\Models\BatchLevel;
use App\Models\Book;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LinkType;
use App\Models\User;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = User::role(User::ROLE_TEACHER)->get();
        $linkTypes = LinkType::all();

        // A few batch levels admins commonly use.
        $levels = collect(['Level 1', 'Level 2', 'Level 3', 'Advanced Level'])
            ->map(fn ($name) => BatchLevel::firstOrCreate(['name' => $name]));

        // Give ~10 of the 24 seeded courses one or two batches each.
        Course::inRandomOrder()->limit(10)->get()->each(function (Course $course) use ($teachers, $levels, $linkTypes) {
            for ($i = 1; $i <= fake()->numberBetween(1, 2); $i++) {
                $batch = Batch::create([
                    'batchable_type' => Course::class,
                    'batchable_id' => $course->id,
                    'teacher_id' => $teachers->random()->id,
                    'batch_level_id' => $levels->random()->id,
                    'batch_number' => 'B-' . fake()->unique()->numerify('####'),
                    'batch_name' => $course->title . ' — Batch ' . $i,
                    'class_start_time' => fake()->randomElement(['09:00:00', '14:00:00', '19:00:00']),
                    'class_end_time' => fake()->randomElement(['11:00:00', '16:00:00', '21:00:00']),
                    'batch_days' => fake()->randomElement([
                        ['Sat', 'Sun', 'Mon'],
                        ['Sun', 'Tue', 'Thu'],
                        ['Fri', 'Sat'],
                    ]),
                    'weekly_days' => 3,
                    'batch_started_date' => now()->subDays(fake()->numberBetween(0, 20)),
                    'batch_end_date' => now()->addMonths(3),
                    'student_limit' => 30,
                    'free_or_paid' => true,
                    'upcoming_status' => fake()->boolean(70),
                    'hide_batch' => false,
                    'status' => true,
                ]);

                // Assign a few already-enrolled students in this course to the batch.
                CourseEnrollment::where('course_id', $course->id)
                    ->whereNull('batch_id')
                    ->inRandomOrder()
                    ->limit(fake()->numberBetween(2, 5))
                    ->get()
                    ->each(fn ($enrollment) => $enrollment->update(['batch_id' => $batch->id]));

                // A couple of class links per batch, including one for today so the
                // student "class link" page has something real to show immediately.
                foreach ([now(), now()->addWeek()] as $classDate) {
                    BatchClass::create([
                        'link_type_id' => $linkTypes->random()->id,
                        'batchable_type' => Course::class,
                        'batchable_id' => $course->id,
                        'teacher_id' => $batch->teacher_id,
                        'batch_id' => $batch->id,
                        'full_link' => 'https://zoom.us/j/' . fake()->numerify('##########'),
                        'metting_code' => fake()->numerify('###-####-####'),
                        'metting_pass_code' => fake()->bothify('??####'),
                        'class_start_time' => $classDate->copy()->setTimeFromTimeString($batch->class_start_time->format('H:i:s')),
                        'class_end_time' => $classDate->copy()->setTimeFromTimeString($batch->class_end_time->format('H:i:s')),
                        'class_note' => 'Please join 5 minutes early.',
                        'status' => true,
                    ]);
                }
            }
        });

        // A couple of book batches too, since the same system serves books.
        Book::inRandomOrder()->limit(3)->get()->each(function (Book $book) use ($teachers, $levels, $linkTypes) {
            Batch::create([
                'batchable_type' => Book::class,
                'batchable_id' => $book->id,
                'teacher_id' => $teachers->random()->id,
                'batch_level_id' => $levels->random()->id,
                'batch_number' => 'B-' . fake()->unique()->numerify('####'),
                'batch_name' => $book->title . ' — Reading Group',
                'class_start_time' => '18:00:00',
                'class_end_time' => '19:30:00',
                'batch_days' => ['Wed'],
                'weekly_days' => 1,
                'batch_started_date' => now(),
                'batch_end_date' => now()->addMonths(2),
                'student_limit' => 20,
                'free_or_paid' => true,
                'upcoming_status' => true,
                'hide_batch' => false,
                'status' => true,
            ]);
        });
    }
}
