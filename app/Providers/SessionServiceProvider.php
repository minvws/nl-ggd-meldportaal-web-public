<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\SingleSession;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        App::Bind(SingleSession::class, function ($app) {
            return new SingleSession();
        });
    }
}
