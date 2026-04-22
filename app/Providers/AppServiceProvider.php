<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('order-event', function ($app) {
            return new \App\Services\OrderEventService();
        });

        $this->app->bind('message', function ($app) {
            return new \App\Services\MessageService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super-Admin" role all permission checks using can()
        Gate::before(function ($user, $ability)
        {
            return $user->hasRole('Super-Admin') ? true : null;
        });

        Gate::define('access-settings', function ($user) {
            return $user->type !== 'G';
        });
    }
}
