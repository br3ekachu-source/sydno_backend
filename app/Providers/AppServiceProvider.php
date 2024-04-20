<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/auth/reset-password?token=$token&email={$notifiable->getEmailForPasswordReset()}";
        });
        // if (env('APP_ENV') !== 'local') {
        //     URL::forceScheme('https');
        //     URL::forceRootUrl(config('app.url', env('APP_URL')));
        // }
    }
}
