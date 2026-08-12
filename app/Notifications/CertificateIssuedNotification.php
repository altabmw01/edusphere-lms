<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Certificate $certificate)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Certificate is Ready!')
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("You've completed \"{$this->certificate->course->title}\" and your certificate is ready.")
            ->action('Download Certificate', route('student.certificates.download', $this->certificate));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'certificate_id' => $this->certificate->id,
            'message' => "Your certificate for \"{$this->certificate->course->title}\" is ready.",
        ];
    }
}
