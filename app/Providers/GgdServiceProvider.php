<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\GgdService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class GgdServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(GgdService::class, function () {
            $client = new Client([
                'base_uri' => config('ggd.host'),
                'timeout' => 10.0,
                'connect_timeout' => 10.0,
            ]);

            return new GgdService($client, config('ggd.credentials.client'), config('ggd.credentials.secret'));
        });
    }
}
