<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ContactMessage $message)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Contact Message: ' . $this->message->subject)
            ->greeting('New inbound message')
            ->line("From: {$this->message->name} ({$this->message->email})")
            ->line($this->message->message)
            ->action('View in Admin Inbox', route('admin.messages.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'contact_message_id' => $this->message->id,
            'message' => "New contact message from {$this->message->name}: {$this->message->subject}",
        ];
    }
}
