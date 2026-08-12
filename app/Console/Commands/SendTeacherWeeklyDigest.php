<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Models\BatchClass;
use App\Models\BookPurchase;
use App\Models\CourseEnrollment;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\TeacherWeeklyDigestNotification;
use Illuminate\Console\Command;

class SendTeacherWeeklyDigest extends Command
{
    protected $signature = 'lms:teacher-weekly-digest';

    protected $description = 'Email every active teacher a summary of last week\'s new batch students, revenue, and class links added.';

    public function handle(): int
    {
        $since = now()->subDays(7);
        $sent = 0;

        User::query()->role(User::ROLE_TEACHER)->active()->each(function (User $teacher) use ($since, &$sent) {
            $batchIds = Batch::where('teacher_id', $teacher->id)->pluck('id');

            if ($batchIds->isEmpty()) {
                return;
            }

            $newStudents = CourseEnrollment::whereIn('batch_id', $batchIds)->where('created_at', '>=', $since)->count()
                + BookPurchase::whereIn('batch_id', $batchIds)->where('created_at', '>=', $since)->count();

            $weeklyRevenue = (float) OrderItem::whereIn('batch_id', $batchIds)
                ->where('created_at', '>=', $since)
                ->sum('line_total');

            $classesAdded = BatchClass::whereIn('batch_id', $batchIds)
                ->where('created_at', '>=', $since)
                ->count();

            if ($newStudents === 0 && $weeklyRevenue == 0.0 && $classesAdded === 0) {
                return;
            }

            $teacher->notify(new TeacherWeeklyDigestNotification($newStudents, $weeklyRevenue, $classesAdded));
            $sent++;
        });

        $this->info("Weekly digest sent to {$sent} teacher(s).");

        return self::SUCCESS;
    }
}
