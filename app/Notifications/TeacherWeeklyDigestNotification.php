<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherWeeklyDigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $newEnrollments,
        protected float $weeklyRevenue,
        protected int $newReviews,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Weekly EduSphere Teaching Summary')
            ->greeting("Hi {$notifiable->name},")
            ->line("Here's how your courses performed over the last 7 days:")
            ->line("New enrollments: {$this->newEnrollments}")
            ->line('Revenue earned: ' . config('lms.currency_symbol') . number_format($this->weeklyRevenue, 2))
            ->line("New student reviews: {$this->newReviews}")
            ->action('View Teacher Dashboard', route('teacher.dashboard'))
            ->line('Keep up the great work!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'new_enrollments' => $this->newEnrollments,
            'weekly_revenue' => $this->weeklyRevenue,
            'new_reviews' => $this->newReviews,
            'message' => "You had {$this->newEnrollments} new enrollment(s) this week.",
        ];
    }
}
