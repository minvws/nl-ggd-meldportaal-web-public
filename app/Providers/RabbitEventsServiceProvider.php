<?php

declare(strict_types=1);

namespace App\Providers;

class RabbitEventsServiceProvider extends \RabbitEvents\Foundation\RabbitEventsServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
        //
    }
}
