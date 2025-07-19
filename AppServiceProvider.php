<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Auth\Notifications\ResetPassword;
use App\View\Components\StatCard;
use App\View\Components\NotificationBell;
use App\View\Components\UserDropdown;
use App\View\Components\Icon\Calendar;
use App\View\Components\Icon\Pencil;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Register the Blade component globally
        Blade::component('stat-card', StatCard::class);
        Blade::component('notification-bell', NotificationBell::class);
        Blade::component('user-dropdown', UserDropdown::class);
        Blade::component('icon-calendar', Calendar::class);
        Blade::component('icon-pencil', Pencil::class);
        


        // Password reset link customization (for API/SPA setup)
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}

