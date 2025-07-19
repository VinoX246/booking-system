<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The booking instance.
     *
     * @var \App\Models\Booking
     */
    public $booking;

    /**
     * The changes made to the booking.
     *
     * @var array
     */
    public $changes;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Booking  $booking
     * @param  array  $changes
     * @return void
     */
    public function __construct(Booking $booking, array $changes = [])
    {
        $this->booking = $booking->loadMissing(['user', 'service', 'staff']);
        $this->changes = $changes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('bookings'), // For admin/staff notifications
            new PrivateChannel('user.' . $this->booking->user_id), // For customer
            new PrivateChannel('staff.' . $this->booking->staff_id), // For assigned staff
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'booking.updated';
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
            'status' => $this->booking->status,
            'changes' => $this->changes,
            'user' => [
                'id' => $this->booking->user->id,
                'name' => $this->booking->user->name,
            ],
            'staff' => $this->booking->staff ? [
                'id' => $this->booking->staff->id,
                'name' => $this->booking->staff->name,
            ] : null,
            'service' => [
                'id' => $this->booking->service->id,
                'name' => $this->booking->service->name,
                'duration' => $this->booking->service->duration,
            ],
            'date' => $this->booking->booking_date->format('Y-m-d'),
            'time' => $this->booking->start_time,
            'end_time' => $this->booking->end_time,
            'updated_at' => $this->booking->updated_at->toDateTimeString(),
            'message' => $this->getNotificationMessage(),
        ];
    }

    /**
     * Generate appropriate notification message based on changes.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        if (isset($this->changes['status'])) {
            return match($this->changes['status']) {
                'confirmed' => 'Your booking has been confirmed',
                'cancelled' => 'Your booking has been cancelled',
                'rescheduled' => 'Your booking has been rescheduled',
                default => 'Your booking status has been updated',
            };
        }

        if (isset($this->changes['booking_date']) || isset($this->changes['start_time'])) {
            return 'Your booking time has been changed';
        }

        return 'Your booking details have been updated';
    }

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen(): bool
    {
        return $this->booking->wasChanged() && 
               config('broadcasting.default') !== 'null';
    }
}
