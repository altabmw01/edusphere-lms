<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use App\Notifications\TeacherWeeklyDigestNotification;
use Illuminate\Console\Command;

class SendTeacherWeeklyDigest extends Command
{
    protected $signature = 'lms:teacher-weekly-digest';

    protected $description = 'Email every active teacher a summary of last week\'s enrollments, revenue, and reviews.';

    public function handle(): int
    {
        $since = now()->subDays(7);
        $sent = 0;

        User::query()->role(User::ROLE_TEACHER)->active()->each(function (User $teacher) use ($since, &$sent) {
            $courseIds = Course::where('teacher_id', $teacher->id)->pluck('id');

            if ($courseIds->isEmpty()) {
                return;
            }

            $newEnrollments = CourseEnrollment::whereIn('course_id', $courseIds)
                ->where('created_at', '>=', $since)
                ->count();

            $weeklyRevenue = (float) OrderItem::whereIn('purchasable_id', $courseIds)
                ->where('purchasable_type', Course::class)
                ->where('created_at', '>=', $since)
                ->sum('line_total');

            $newReviews = Review::where('reviewable_type', Course::class)
                ->whereIn('reviewable_id', $courseIds)
                ->where('created_at', '>=', $since)
                ->count();

            if ($newEnrollments === 0 && $weeklyRevenue == 0.0 && $newReviews === 0) {
                return;
            }

            $teacher->notify(new TeacherWeeklyDigestNotification($newEnrollments, $weeklyRevenue, $newReviews));
            $sent++;
        });

        $this->info("Weekly digest sent to {$sent} teacher(s).");

        return self::SUCCESS;
    }
}
