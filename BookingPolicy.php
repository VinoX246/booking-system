<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        // Optionally restrict based on user role
        return true;
    }

    public function view(User $user, Booking $booking): Response
    {
        return $user->id === $booking->user_id
            ? Response::allow()
            : Response::deny('You do not own this booking.');
    }

    public function create(User $user): bool
    {
        // Example: Only verified users can create bookings
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Booking $booking): Response
    {
        if ($booking->start_time->isPast()) {
            return Response::deny('Cannot modify past bookings.');
        }

        return $user->id === $booking->user_id
            ? Response::allow()
            : Response::deny('You can only edit your own bookings.');
    }

    public function delete(User $user, Booking $booking): Response
    {
        if ($booking->start_time->isPast()) {
            return Response::deny('Cannot delete past bookings.');
        }

        if ($booking->has_payments) {
            return Response::deny('Cannot delete booking with payments.');
        }

        return $user->id === $booking->user_id
            ? Response::allow()
            : Response::deny('You can only delete your own bookings.');
    }

    // Optional: Additional custom policy methods
    public function cancel(User $user, Booking $booking): Response
    {
        if (!$booking->is_refundable) {
            return Response::deny('This booking cannot be canceled.');
        }

        return $this->update($user, $booking);
    }
}

