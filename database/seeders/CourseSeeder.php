<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role(User::ROLE_ADMIN)->first();
        $categories = Category::type('course')->get();
        $students = User::role(User::ROLE_STUDENT)->get();

        // 24 fully-fleshed courses spread across every category.
        Course::factory()
            ->count(24)
            ->create()
            ->each(function (Course $course, int $index) use ($admin, $categories, $students) {
                $course->update([
                    'created_by' => $admin->id,
                    'category_id' => $categories->random()->id,
                ]);

                // Build 3 sections x 4 lessons = 12 lessons per course.
                $totalMinutes = 0;
                for ($s = 1; $s <= 3; $s++) {
                    $section = $course->sections()->create([
                        'title' => "Module {$s}: " . fake()->words(3, true),
                        'sort_order' => $s,
                    ]);

                    for ($l = 1; $l <= 4; $l++) {
                        $duration = fake()->numberBetween(8, 35);
                        $totalMinutes += $duration;

                        $section->lessons()->create([
                            'course_id' => $course->id,
                            'title' => fake()->sentence(4),
                            'type' => fake()->randomElement(['video', 'video', 'video', 'text', 'pdf', 'quiz']),
                            'content_text' => fake()->paragraphs(2, true),
                            'duration_minutes' => $duration,
                            'is_preview' => $s === 1 && $l === 1,
                            'sort_order' => $l,
                        ]);
                    }
                }

                $course->update([
                    'lessons_count' => 12,
                    'duration_minutes' => $totalMinutes,
                ]);

                // Enroll a handful of random students with varying progress.
                $enrolledStudents = $students->random(min(8, $students->count()));
                foreach ($enrolledStudents as $student) {
                    $progress = fake()->randomElement([15, 40, 65, 80, 100, 100]);

                    $enrollment = CourseEnrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'progress_percent' => $progress,
                        'completed_at' => $progress >= 100 ? now()->subDays(fake()->numberBetween(1, 60)) : null,
                    ]);

                    $lessonsToComplete = $course->lessons()->inRandomOrder()
                        ->limit((int) round(12 * $progress / 100))
                        ->get();

                    foreach ($lessonsToComplete as $lesson) {
                        LessonProgress::create([
                            'user_id' => $student->id,
                            'course_lesson_id' => $lesson->id,
                            'is_completed' => true,
                            'completed_at' => now()->subDays(fake()->numberBetween(1, 60)),
                        ]);
                    }
                }
            });
    }
}
