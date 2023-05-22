<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\CreateMigrationsTableAfterSchemaLoadedListener;
use App\Listeners\LogLogoutEvent;
use App\Listeners\SingleSession;
use App\Listeners\StoreFailedLoginActivity;
use App\Listeners\StoreLastLogin;
use App\Listeners\StoreLoginActivity;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Events\SchemaLoaded;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Failed::class => [
            StoreFailedLoginActivity::class,
        ],
        Login::class => [
            StoreLoginActivity::class,
            SingleSession::class,
            StoreLastLogin::class,
        ],
        Logout::class => [
            LogLogoutEvent::class,
        ],
        SchemaLoaded::class => [
            CreateMigrationsTableAfterSchemaLoadedListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
