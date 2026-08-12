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
        protected int $newStudents,
        protected float $weeklyRevenue,
        protected int $classesAdded,
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
            ->line("Here's how your batches performed over the last 7 days:")
            ->line("New students assigned to your batches: {$this->newStudents}")
            ->line('Revenue from your batches: ' . config('lms.currency_symbol') . number_format($this->weeklyRevenue, 2))
            ->line("Class links you added: {$this->classesAdded}")
            ->action('View Teacher Dashboard', route('teacher.dashboard'))
            ->line('Keep up the great work!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'new_students' => $this->newStudents,
            'weekly_revenue' => $this->weeklyRevenue,
            'classes_added' => $this->classesAdded,
            'message' => "You had {$this->newStudents} new student(s) assigned this week.",
        ];
    }
}
