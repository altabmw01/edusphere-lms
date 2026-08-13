<?php

namespace Tests\Feature\Courses;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Courses (including curriculum) are entirely admin-managed. Teachers have no
 * ownership relationship to a course — their only connection to course/book
 * content is being assigned to teach a Batch of it (covered in Batch tests).
 */
class CourseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_teacher_cannot_access_any_course_management_routes(): void
    {
        $teacher = User::factory()->teacher()->create();
        $course = Course::factory()->create();

        $this->actingAs($teacher)->get(route('admin.courses.index'))->assertForbidden();
        $this->actingAs($teacher)->get(route('admin.courses.edit', $course))->assertForbidden();
        $this->actingAs($teacher)->get(route('admin.courses.curriculum.edit', $course))->assertForbidden();
    }

    public function test_a_manager_can_view_courses_but_cannot_edit_them(): void
    {
        $manager = User::factory()->manager()->create();
        $course = Course::factory()->create();

        $this->actingAs($manager)->get(route('admin.courses.index'))->assertForbidden();
        $this->actingAs($manager)->get(route('admin.courses.edit', $course))->assertForbidden();
    }

    public function test_an_admin_can_create_a_course_and_is_recorded_as_its_creator(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['type' => 'course']);

        $this->actingAs($admin)->post(route('admin.courses.store'), [
            'title' => 'New Laravel Course',
            'category_id' => $category->id,
            'description' => 'A great course about Laravel.',
            'price' => 49,
            'level' => 'beginner',
            'language' => 'English',
            'duration_minutes' => 120,
            'status' => 'published',
        ]);

        $course = Course::where('title', 'New Laravel Course')->first();

        $this->assertNotNull($course);
        $this->assertSame($admin->id, $course->created_by);
    }

    public function test_an_admin_can_edit_and_delete_any_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->actingAs($admin)->get(route('admin.courses.edit', $course))->assertOk();

        $this->actingAs($admin)->delete(route('admin.courses.destroy', $course));
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }

    public function test_an_admin_can_manage_curriculum_sections_and_lessons(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->actingAs($admin)->get(route('admin.courses.curriculum.edit', $course))->assertOk();

        $this->actingAs($admin)->post(route('admin.courses.curriculum.sections.store', $course), [
            'title' => 'Module 1: Getting Started',
        ]);

        $section = CourseSection::where('course_id', $course->id)->first();
        $this->assertNotNull($section);

        $this->actingAs($admin)->post(route('admin.courses.curriculum.lessons.store', [$course, $section]), [
            'title' => 'Introduction',
            'type' => 'video',
            'duration_minutes' => 12,
        ]);

        $this->assertDatabaseHas('course_lessons', [
            'course_section_id' => $section->id,
            'title' => 'Introduction',
        ]);
    }

    public function test_courses_table_has_no_teacher_column(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('courses', 'teacher_id'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('courses', 'created_by'));
    }
}
