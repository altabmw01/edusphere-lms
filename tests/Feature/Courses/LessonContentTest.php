<?php

namespace Tests\Feature\Courses;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LessonContentTest extends TestCase
{
    use RefreshDatabase;

    private function makeSection(): CourseSection
    {
        $course = Course::factory()->create();

        return CourseSection::create(['course_id' => $course->id, 'title' => 'Section 1', 'sort_order' => 1]);
    }

    public function test_admin_can_add_a_video_lesson_with_a_youtube_link_and_no_file(): void
    {
        $admin = User::factory()->admin()->create();
        $section = $this->makeSection();

        $this->actingAs($admin)->post(route('admin.courses.curriculum.lessons.store', [$section->course_id, $section]), [
            'title' => 'Intro Video',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => 10,
        ]);

        $lesson = CourseLesson::where('title', 'Intro Video')->first();
        $this->assertNotNull($lesson);
        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $lesson->video_url);
        $this->assertNull($lesson->content_path);
        $this->assertSame('https://www.youtube.com/embed/dQw4w9WgXcQ', $lesson->embed_url);
        $this->assertSame('YouTube', $lesson->video_platform);
    }

    public function test_a_non_youtube_non_vimeo_video_link_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $section = $this->makeSection();

        $response = $this->actingAs($admin)->post(route('admin.courses.curriculum.lessons.store', [$section->course_id, $section]), [
            'title' => 'Bad Link',
            'type' => 'video',
            'video_url' => 'https://example.com/some-video.mp4',
            'duration_minutes' => 10,
        ]);

        $response->assertSessionHasErrors('video_url');
        $this->assertDatabaseMissing('course_lessons', ['title' => 'Bad Link']);
    }

    public function test_admin_can_add_a_pdf_lesson_with_an_uploaded_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $section = $this->makeSection();

        $this->actingAs($admin)->post(route('admin.courses.curriculum.lessons.store', [$section->course_id, $section]), [
            'title' => 'Worksheet',
            'type' => 'pdf',
            'content_file' => UploadedFile::fake()->create('worksheet.pdf', 100, 'application/pdf'),
            'duration_minutes' => 5,
        ]);

        $lesson = CourseLesson::where('title', 'Worksheet')->first();
        $this->assertNotNull($lesson);
        $this->assertNotNull($lesson->content_path);
        Storage::disk('public')->assertExists($lesson->content_path);
    }

    public function test_a_pdf_lesson_without_a_file_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $section = $this->makeSection();

        $response = $this->actingAs($admin)->post(route('admin.courses.curriculum.lessons.store', [$section->course_id, $section]), [
            'title' => 'No File',
            'type' => 'pdf',
            'duration_minutes' => 5,
        ]);

        $response->assertSessionHasErrors('content_file');
        $this->assertDatabaseMissing('course_lessons', ['title' => 'No File']);
    }

    public function test_admin_can_edit_a_lesson_and_switch_its_type(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $section = $this->makeSection();

        $lesson = $section->lessons()->create([
            'course_id' => $section->course_id,
            'title' => 'Old Title',
            'type' => 'video',
            'video_url' => 'https://youtu.be/abc12345678',
            'duration_minutes' => 8,
        ]);

        $this->actingAs($admin)->put(route('admin.courses.curriculum.lessons.update', [$section->course_id, $lesson]), [
            'title' => 'New Title',
            'type' => 'text',
            'content_text' => 'Updated lesson content.',
            'duration_minutes' => 12,
        ]);

        $lesson->refresh();
        $this->assertSame('New Title', $lesson->title);
        $this->assertSame('text', $lesson->type);
        $this->assertSame('Updated lesson content.', $lesson->content_text);
        $this->assertNull($lesson->video_url);
    }

    public function test_a_teacher_cannot_manage_curriculum(): void
    {
        $teacher = User::factory()->teacher()->create();
        $section = $this->makeSection();

        $this->actingAs($teacher)
            ->post(route('admin.courses.curriculum.lessons.store', [$section->course_id, $section]), [
                'title' => 'Sneaky Lesson',
                'type' => 'text',
                'content_text' => 'x',
                'duration_minutes' => 1,
            ])
            ->assertForbidden();
    }

    public function test_guest_can_download_a_pdf_lesson_marked_as_free_preview(): void
    {
        Storage::fake('public');
        $course = Course::factory()->create();
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'S1', 'sort_order' => 1]);
        $path = Storage::disk('public')->putFile('courses/lessons', UploadedFile::fake()->create('preview.pdf'));

        $lesson = $section->lessons()->create([
            'course_id' => $course->id,
            'title' => 'Free Sample',
            'type' => 'pdf',
            'content_path' => $path,
            'duration_minutes' => 5,
            'is_preview' => true,
        ]);

        $this->get(route('lessons.download', $lesson))->assertOk();
    }

    public function test_guest_cannot_download_a_locked_pdf_lesson(): void
    {
        Storage::fake('public');
        $course = Course::factory()->create();
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'S1', 'sort_order' => 1]);
        $path = Storage::disk('public')->putFile('courses/lessons', UploadedFile::fake()->create('locked.pdf'));

        $lesson = $section->lessons()->create([
            'course_id' => $course->id,
            'title' => 'Locked Lesson',
            'type' => 'pdf',
            'content_path' => $path,
            'duration_minutes' => 5,
            'is_preview' => false,
        ]);

        $this->get(route('lessons.download', $lesson))->assertForbidden();
    }

    public function test_an_enrolled_student_can_download_any_pdf_lesson_in_their_course(): void
    {
        Storage::fake('public');
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();
        \App\Models\CourseEnrollment::create(['user_id' => $student->id, 'course_id' => $course->id]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'S1', 'sort_order' => 1]);
        $path = Storage::disk('public')->putFile('courses/lessons', UploadedFile::fake()->create('full-access.pdf'));

        $lesson = $section->lessons()->create([
            'course_id' => $course->id,
            'title' => 'Deep Dive',
            'type' => 'pdf',
            'content_path' => $path,
            'duration_minutes' => 5,
            'is_preview' => false,
        ]);

        $this->actingAs($student)->get(route('lessons.download', $lesson))->assertOk();
    }
}
