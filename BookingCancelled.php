<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The cancelled booking instance.
     *
     * @var \App\Models\Booking
     */
    public $booking;

    /**
     * The cancellation reason.
     *
     * @var string|null
     */
    public $reason;

    /**
     * The user who initiated the cancellation.
     *
     * @var \App\Models\User|null
     */
    public $cancelledBy;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Booking  $booking
     * @param  string|null  $reason
     * @param  \App\Models\User|null  $cancelledBy
     */
    public function __construct(Booking $booking, ?string $reason = null, ?User $cancelledBy = null)
    {
        $this->booking = $booking->loadMissing(['user', 'service', 'staff']);
        $this->reason = $reason;
        $this->cancelledBy = $cancelledBy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('bookings'), // Admin channel
            new PrivateChannel('user.' . $this->booking->user_id), // Customer
        ];

        if ($this->booking->staff_id) {
            $channels[] = new PrivateChannel('staff.' . $this->booking->staff_id); // Staff member
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'booking.cancelled';
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
            'service' => [
                'name' => $this->booking->service->name,
                'duration' => $this->booking->service->duration,
            ],
            'original_date' => $this->booking->booking_date->format('Y-m-d'),
            'original_time' => $this->booking->start_time,
            'cancelled_at' => now()->toDateTimeString(),
            'reason' => $this->reason,
            'initiated_by' => $this->cancelledBy ? [
                'id' => $this->cancelledBy->id,
                'name' => $this->cancelledBy->name,
                'type' => $this->getUserType($this->cancelledBy),
            ] : null,
            'customer' => [
                'id' => $this->booking->user->id,
                'name' => $this->booking->user->name,
            ],
            'refund_status' => $this->getRefundStatus(),
            'message' => $this->getNotificationMessage(),
        ];
    }

    /**
     * Determine user type for cancellation initiator.
     */
    protected function getUserType(User $user): string
    {
        if ($user->isAdmin()) {
            return 'admin';
        }

        if ($user->isStaff()) {
            return 'staff';
        }

        return 'customer';
    }

    /**
     * Calculate refund status.
     */
    protected function getRefundStatus(): string
    {
        if (!$this->booking->payment) {
            return 'not_applicable';
        }

        $hoursUntilBooking = now()->diffInHours($this->booking->start_time);

        if ($hoursUntilBooking > 24) {
            return 'full_refund_pending';
        }

        if ($hoursUntilBooking > 1) {
            return 'partial_refund_pending';
        }

        return 'no_refund';
    }

    /**
     * Generate appropriate notification message.
     */
    protected function getNotificationMessage(): string
    {
        if ($this->cancelledBy && $this->cancelledBy->id !== $this->booking->user_id) {
            return sprintf(
                'Your booking for %s on %s has been cancelled by %s',
                $this->booking->service->name,
                $this->booking->booking_date->format('M j, Y'),
                $this->cancelledBy->name
            );
        }

        return sprintf(
            'Your booking for %s on %s has been cancelled',
            $this->booking->service->name,
            $this->booking->booking_date->format('M j, Y')
        );
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return config('broadcasting.default') !== 'null';
    }
}
