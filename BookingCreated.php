<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The booking instance.
     *
     * @var \App\Models\Booking
     */
    public $booking;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load(['user', 'service']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('bookings'), // For admin notifications
            new PrivateChannel('user.' . $this->booking->user_id), // For user-specific updates
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'booking.created';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->booking->id,
            'user' => [
                'name' => $this->booking->user->name,
                'email' => $this->booking->user->email,
            ],
            'service' => $this->booking->service->name,
            'date' => $this->booking->booking_date->format('Y-m-d'),
            'time' => $this->booking->start_time,
            'status' => $this->booking->status,
            'created_at' => $this->booking->created_at->toDateTimeString(),
            'message' => 'New booking created for ' . $this->booking->service->name,
        ];
    }

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen(): bool
    {
        return config('broadcasting.default') !== 'null';
    }
}
