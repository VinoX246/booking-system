<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'status',
        'requires_approval',
        'approved'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'requires_approval' => 'boolean',
        'approved' => 'boolean'
    ];

    protected $appends = [
        'is_refundable',
        'duration_minutes',
        'is_active'
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Determine if booking is refundable
     */
    protected function isRefundable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_time->isFuture() && 
                         $this->status !== self::STATUS_CANCELLED
        );
    }

    /**
     * Calculate duration in minutes
     */
    protected function durationMinutes(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->end_time->diffInMinutes($this->start_time)
        );
    }

    /**
     * Check if booking is currently active
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => now()->between($this->start_time, $this->end_time)
        );
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    /**
     * Scope for past bookings
     */
    public function scopePast($query)
    {
        return $query->where('end_time', '<', now());
    }

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Approve the booking
     */
    public function approve()
    {
        $this->update([
            'approved' => true,
            'status' => self::STATUS_CONFIRMED
        ]);
        
        return $this;
    }
}
