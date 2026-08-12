<?php

namespace Tests\Feature\Reviews;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    private function pendingCourseReview(?int $teacherId = null): Review
    {
        $course = Course::factory()->create(['teacher_id' => $teacherId ?? User::factory()->teacher()->create()->id]);
        $student = User::factory()->student()->create();

        return Review::create([
            'user_id' => $student->id,
            'reviewable_type' => Course::class,
            'reviewable_id' => $course->id,
            'rating' => 4,
            'comment' => 'Pretty good course overall.',
            'status' => 'pending',
        ]);
    }

    public function test_a_student_cannot_moderate_reviews(): void
    {
        $student = User::factory()->student()->create();
        $review = $this->pendingCourseReview();

        $this->actingAs($student)
            ->put(route('manager.reviews.approve', $review))
            ->assertForbidden();
    }

    public function test_a_manager_can_approve_a_pending_review_and_it_recalculates_the_course_rating(): void
    {
        $manager = User::factory()->manager()->create();
        $review = $this->pendingCourseReview();

        $this->actingAs($manager)
            ->put(route('manager.reviews.approve', $review))
            ->assertRedirect();

        $this->assertSame('approved', $review->fresh()->status);

        $course = $review->reviewable;
        $this->assertSame(1, $course->fresh()->rating_count);
        $this->assertSame('4.00', number_format($course->fresh()->rating_avg, 2));
    }

    public function test_a_manager_can_reject_a_review(): void
    {
        $manager = User::factory()->manager()->create();
        $review = $this->pendingCourseReview();

        $this->actingAs($manager)->put(route('manager.reviews.reject', $review));

        $this->assertSame('rejected', $review->fresh()->status);
    }

    public function test_the_owning_teacher_can_reply_to_a_review_on_their_own_course(): void
    {
        $teacher = User::factory()->teacher()->create();
        $review = $this->pendingCourseReview(teacherId: $teacher->id);

        $this->actingAs($teacher)
            ->post(route('teacher.reviews.reply', $review), ['reply' => 'Thanks for the feedback!'])
            ->assertRedirect();

        $this->assertSame('Thanks for the feedback!', $review->fresh()->reply);
    }

    public function test_a_teacher_cannot_reply_to_a_review_on_someone_elses_course(): void
    {
        $teacher = User::factory()->teacher()->create();
        $review = $this->pendingCourseReview(); // owned by a different, auto-generated teacher

        $this->actingAs($teacher)
            ->post(route('teacher.reviews.reply', $review), ['reply' => 'Not my course!'])
            ->assertForbidden();
    }

    public function test_a_student_can_only_review_a_course_they_have_purchased(): void
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();

        $this->actingAs($student)
            ->post(route('student.reviews.courses.store', $course), ['rating' => 5, 'comment' => 'Loved it!'])
            ->assertForbidden();

        $this->assertDatabaseMissing('reviews', ['user_id' => $student->id, 'reviewable_id' => $course->id]);
    }
}
