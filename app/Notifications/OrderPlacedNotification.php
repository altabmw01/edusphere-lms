<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order Confirmation - {$this->order->order_number}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Thank you for your order #{$this->order->order_number}.")
            ->line("Order total: " . config('lms.currency_symbol') . number_format($this->order->grand_total, 2))
            ->action('View Order', route('student.orders.show', $this->order->order_number))
            ->line('Thank you for learning with EduSphere!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'grand_total' => $this->order->grand_total,
            'message' => "Your order #{$this->order->order_number} has been placed.",
        ];
    }
}
