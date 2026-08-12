<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCourseEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Course $course, protected User $student)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Student Enrolled')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->student->name} just enrolled in your course \"{$this->course->title}\".");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'student_name' => $this->student->name,
            'message' => "{$this->student->name} enrolled in \"{$this->course->title}\".",
        ];
    }
}
