<?php

namespace Tests\Feature\Courses;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseProgressTest extends TestCase
{
    use RefreshDatabase;

    private function makeCourseWithLessons(int $lessonCount, bool $hasCertificate = true): Course
    {
        $course = Course::factory()->create(['has_certificate' => $hasCertificate]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section 1', 'sort_order' => 1]);

        for ($i = 1; $i <= $lessonCount; $i++) {
            CourseLesson::create([
                'course_id' => $course->id,
                'course_section_id' => $section->id,
                'title' => "Lesson {$i}",
                'type' => 'video',
                'duration_minutes' => 10,
                'sort_order' => $i,
            ]);
        }

        return $course;
    }

    public function test_a_student_cannot_complete_a_lesson_for_a_course_they_have_not_purchased(): void
    {
        $student = User::factory()->student()->create();
        $course = $this->makeCourseWithLessons(2);
        $lesson = $course->lessons()->first();

        $this->actingAs($student)
            ->post(route('student.lessons.complete', $lesson))
            ->assertForbidden();
    }

    public function test_completing_all_lessons_brings_progress_to_100_percent(): void
    {
        Storage::fake('public');

        $student = User::factory()->student()->create();
        $course = $this->makeCourseWithLessons(2);
        CourseEnrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'progress_percent' => 0]);

        foreach ($course->lessons as $lesson) {
            $this->actingAs($student)->post(route('student.lessons.complete', $lesson));
        }

        $enrollment = CourseEnrollment::where('user_id', $student->id)->where('course_id', $course->id)->first();

        $this->assertSame('100.00', number_format($enrollment->progress_percent, 2));
        $this->assertNotNull($enrollment->completed_at);
    }

    public function test_a_certificate_is_automatically_issued_when_a_course_with_certification_is_completed(): void
    {
        Storage::fake('public');

        $student = User::factory()->student()->create();
        $course = $this->makeCourseWithLessons(1, hasCertificate: true);
        CourseEnrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'progress_percent' => 0]);

        $lesson = $course->lessons()->first();
        $this->actingAs($student)->post(route('student.lessons.complete', $lesson));

        $this->assertDatabaseHas('certificates', ['user_id' => $student->id, 'course_id' => $course->id]);

        $certificate = Certificate::where('user_id', $student->id)->where('course_id', $course->id)->first();
        Storage::disk('public')->assertExists($certificate->file_path);
    }

    public function test_no_certificate_is_issued_for_a_course_without_certification_enabled(): void
    {
        Storage::fake('public');

        $student = User::factory()->student()->create();
        $course = $this->makeCourseWithLessons(1, hasCertificate: false);
        CourseEnrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'progress_percent' => 0]);

        $lesson = $course->lessons()->first();
        $this->actingAs($student)->post(route('student.lessons.complete', $lesson));

        $this->assertDatabaseMissing('certificates', ['user_id' => $student->id, 'course_id' => $course->id]);
    }

    public function test_completing_a_lesson_twice_does_not_double_count_progress(): void
    {
        Storage::fake('public');

        $student = User::factory()->student()->create();
        $course = $this->makeCourseWithLessons(4);
        CourseEnrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'progress_percent' => 0]);

        $lesson = $course->lessons()->first();
        $this->actingAs($student)->post(route('student.lessons.complete', $lesson));
        $this->actingAs($student)->post(route('student.lessons.complete', $lesson));

        $this->assertSame(
            1,
            \App\Models\LessonProgress::where('user_id', $student->id)->where('course_lesson_id', $lesson->id)->count()
        );

        $enrollment = CourseEnrollment::where('user_id', $student->id)->where('course_id', $course->id)->first();
        $this->assertSame('25.00', number_format($enrollment->progress_percent, 2));
    }
}
