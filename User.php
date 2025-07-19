<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Roles constants for better maintainability
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';
    public const ROLE_MANAGER = 'manager';

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user has any of given roles
     */
    public function hasRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Get all bookings that require approval
     */
    public function bookingsNeedingApproval()
    {
        return $this->bookings()
            ->where('requires_approval', true)
            ->where('approved', false);
    }
}
