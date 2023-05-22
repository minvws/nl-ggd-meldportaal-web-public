<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\BsnService;
use App\Services\MapTestBrandsToEuValuesService;
use App\Services\TestService;
use Illuminate\Support\ServiceProvider;
use MinVWS\Logging\Laravel\LogService;

class TestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(TestService::class, function () {

            return new TestService(
                $this->app->get(BsnService::class),
                new MapTestBrandsToEuValuesService(),
                $this->app->get(LogService::class)
            );
        });
    }
}
