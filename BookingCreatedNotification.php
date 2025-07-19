<?php

// app/Notifications/BookingCreatedNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Your booking has been created successfully!')
                    ->line('Booking Title: ' . $this->booking->title)
                    ->line('Start Time: ' . $this->booking->start_time)
                    ->line('End Time: ' . $this->booking->end_time)
                    ->action('View Booking', url('/bookings'))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'title' => $this->booking->title,
            'message' => 'Your booking has been created successfully!',
        ];
    }
}

