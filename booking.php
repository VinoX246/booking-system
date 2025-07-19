<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Booking System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the main configuration settings for your booking system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Business Hours
    |--------------------------------------------------------------------------
    */
    'business_hours' => [
        'start' => env('BUSINESS_HOURS_START', 9),  // 9 AM
        'end' => env('BUSINESS_HOURS_END', 17),     // 5 PM
        'days' => [
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => false,
            'sunday' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Slots Configuration
    |--------------------------------------------------------------------------
    */
    'time_slots' => [
        'interval' => env('TIME_SLOT_INTERVAL', 30), // minutes
        'buffer_between_bookings' => env('BUFFER_BETWEEN_BOOKINGS', 15), // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking Rules
    |--------------------------------------------------------------------------
    */
    'rules' => [
        'max_future_days' => env('MAX_BOOKING_DAYS', 90), // days
        'min_advance_notice' => env('MIN_ADVANCE_NOTICE', 2), // hours
        'cancellation_window' => env('CANCELLATION_WINDOW', 24), // hours
        'max_per_user' => env('MAX_BOOKINGS_PER_USER', 5), // number of bookings
        'auto_confirm' => env('AUTO_CONFIRM_BOOKINGS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Calendar Configuration
    |--------------------------------------------------------------------------
    */
    'calendar' => [
        'default_view' => 'week', // day, week, month
        'first_day' => 1, // 0 = Sunday, 1 = Monday
        'slot_duration' => '00:30:00',
        'min_time' => '08:00:00',
        'max_time' => '20:00:00',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'currency' => env('BOOKING_CURRENCY', 'USD'),
        'currency_symbol' => env('BOOKING_CURRENCY_SYMBOL', '$'),
        'deposit_percent' => env('BOOKING_DEPOSIT_PERCENT', 20),
        'refund_policy' => [
            'full_refund_hours' => 48,
            'partial_refund_hours' => 24,
            'no_refund_hours' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications Configuration
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'email' => [
            'customer' => [
                'confirm' => true,
                'reminder' => true,
                'cancellation' => true,
                'reschedule' => true,
            ],
            'staff' => [
                'new_booking' => true,
                'cancellation' => true,
                'reminder' => true,
            ],
            'admin' => [
                'new_booking' => true,
                'cancellation' => true,
            ],
        ],
        'sms' => [
            'reminder_hours_before' => 24,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Integrations
    |--------------------------------------------------------------------------
    */
    'integrations' => [
        'google_calendar' => [
            'enabled' => env('GOOGLE_CALENDAR_ENABLED', false),
            'sync_direction' => 'both', // none, to_google, from_google, both
        ],
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i',
        'display_time_format' => 'g:i A',
        'show_service_images' => true,
        'show_staff_avatars' => true,
    ],
];