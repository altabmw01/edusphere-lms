<?php

namespace Tests\Feature\Courses;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_teacher_can_edit_their_own_course(): void
    {
        $teacher = User::factory()->teacher()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->get(route('teacher.courses.edit', $course))
            ->assertOk();
    }

    public function test_a_teacher_cannot_edit_another_teachers_course(): void
    {
        $teacher = User::factory()->teacher()->create();
        $otherTeacher = User::factory()->teacher()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

        $this->actingAs($teacher)
            ->get(route('teacher.courses.edit', $course))
            ->assertForbidden();
    }

    public function test_a_teacher_cannot_delete_another_teachers_course(): void
    {
        $teacher = User::factory()->teacher()->create();
        $otherTeacher = User::factory()->teacher()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

        $this->actingAs($teacher)
            ->delete(route('teacher.courses.destroy', $course))
            ->assertForbidden();

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_a_teacher_submitted_course_is_always_pending_never_directly_published(): void
    {
        $teacher = User::factory()->teacher()->create();
        $category = Category::factory()->create(['type' => 'course']);

        $this->actingAs($teacher)->post(route('teacher.courses.store'), [
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
        $this->assertSame('pending', $course->status);
        $this->assertSame($teacher->id, $course->teacher_id);
    }

    public function test_a_teacher_cannot_manage_curriculum_for_another_teachers_course(): void
    {
        $teacher = User::factory()->teacher()->create();
        $otherTeacher = User::factory()->teacher()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

        $this->actingAs($teacher)
            ->get(route('teacher.courses.curriculum.edit', $course))
            ->assertForbidden();
    }

    public function test_admin_can_edit_any_course(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($admin)
            ->get(route('admin.courses.edit', $course))
            ->assertOk();
    }
}
