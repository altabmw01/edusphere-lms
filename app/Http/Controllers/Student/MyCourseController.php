<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\LessonProgress;
use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyCourseController extends Controller
{
    public function __construct(protected CertificateService $certificates)
    {
    }

    public function index(): View
    {
        $enrollments = Auth::user()->enrollments()
            ->with('course.category')
            ->latest()
            ->paginate(9);

        return view('student.my-courses.index', ['enrollments' => $enrollments]);
    }

    public function show(Course $course): View
    {
        $user = Auth::user();
        abort_unless($user->hasPurchasedCourse($course->id), 403, 'You have not purchased this course.');

        $course->load(['sections.lessons' => fn ($q) => $q->orderBy('sort_order')]);

        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('course_lesson_id')
            ->all();

        return view('student.my-courses.show', [
            'course' => $course,
            'enrollment' => $user->enrollments()->where('course_id', $course->id)->first(),
            'completedLessonIds' => $completedLessonIds,
        ]);
    }

    public function completeLesson(CourseLesson $lesson): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasPurchasedCourse($lesson->course_id), 403);

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'course_lesson_id' => $lesson->id],
            ['is_completed' => true, 'completed_at' => now()]
        );

        $this->recalculateProgress($user->id, $lesson->course_id);

        return back()->with('status', 'Lesson marked as complete.');
    }

    protected function recalculateProgress(int $userId, int $courseId): void
    {
        $totalLessons = CourseLesson::where('course_id', $courseId)->count();
        $completed = LessonProgress::where('user_id', $userId)
            ->whereIn('course_lesson_id', CourseLesson::where('course_id', $courseId)->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $percent = $totalLessons > 0 ? round(($completed / $totalLessons) * 100, 2) : 0;

        $enrollment = Auth::user()->enrollments()->where('course_id', $courseId)->first();

        if (! $enrollment) {
            return;
        }

        $enrollment->update([
            'progress_percent' => $percent,
            'completed_at' => $percent >= 100 ? ($enrollment->completed_at ?? now()) : null,
        ]);

        if ($percent >= 100) {
            $course = $enrollment->course;
            if ($course->has_certificate && $this->certificates->isEligible(Auth::user(), $course)) {
                $this->certificates->issue(Auth::user(), $course);
            }
        }
    }
}
