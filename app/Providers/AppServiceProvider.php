<?php

namespace App\Providers;

use App\Mail\Transport\Office365OAuthSmtpTransport;
use App\Models\SmtpSetting;
use App\Services\Office365OAuthTokenService;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->afterResolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('office365-oauth2', function (array $config) {
                return new Office365OAuthSmtpTransport(
                    $config,
                    $this->app->make(Office365OAuthTokenService::class)
                );
            });
        });

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
        if ($this->shouldLoadDatabaseBackedSettings()) {
            $this->configureSmtp();
        }

        // Implicitly grant "Super-Admin" role all permission checks using can()
        Gate::before(function ($user, $ability)
        {
            return $user->hasRole('Super-Admin') ? true : null;
        });

        Gate::define('access-settings', function ($user) {
            return $user->type !== 'G';
        });
    }

    protected function shouldLoadDatabaseBackedSettings(): bool
    {
        if ($this->isMissingSqliteDatabaseFile()) {
            return false;
        }

        if (! app()->runningInConsole()) {
            return true;
        }

        $command = $_SERVER['argv'][1] ?? null;
        if (! $command) {
            return true;
        }

        $skip = [
            'package:discover',
            'optimize',
            'optimize:clear',
            'config:cache',
            'config:clear',
            'route:cache',
            'route:clear',
            'view:cache',
            'view:clear',
            'event:cache',
            'event:clear',
        ];

        return ! in_array($command, $skip, true);
    }

    protected function isMissingSqliteDatabaseFile(): bool
    {
        if (config('database.default') !== 'sqlite') {
            return false;
        }

        $database = config('database.connections.sqlite.database');
        if (! is_string($database) || $database === '' || $database === ':memory:') {
            return false;
        }

        $path = $this->isAbsolutePath($database) ? $database : base_path($database);

        return ! is_file($path);
    }

    protected function isAbsolutePath(string $path): bool
    {
        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return true;
        }

        return (bool) preg_match('/^[A-Za-z]:\\\\/', $path);
    }

    protected function configureSmtp(): void
    {
        if (! Schema::hasTable('smtp_settings')) {
            return;
        }

        $setting = SmtpSetting::activeSetting();

        if (! $setting || ! $setting->isConfiguredForProvider()) {
            return;
        }

        Config::set('mail.mailers.smtp', array_merge(
            Config::get('mail.mailers.smtp', []),
            $setting->mailerTransportConfiguration(),
            [
                'local_domain' => parse_url((string) config('app.url', 'http://localhost'), PHP_URL_HOST),
            ]
        ));

        Config::set('mail.default', 'smtp');

        Config::set('mail.from', [
            'address' => $setting->from_email,
            'name' => $setting->from_name ?? config('app.name'),
        ]);
    }
}
