<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Postcode\PostcodeService;
use App\Services\Postcode\Resolver\ApiResolver;
use App\Services\Postcode\Resolver\MockResolver;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class PostcodeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(PostcodeService::class, function () {
            if (config('postalcode.mock')) {
                return new PostcodeService(new MockResolver(), (bool)config('postalcode.enabled'));
            }

            $opts = [
                'base_uri' => config('postalcode.url'),
                'timeout' => 5,
                'connect_timeout' => 5,
                'verify' => config('postalcode.verify_ssl'),
            ];

            if (config('postalcode.mtls.enabled')) {
                $opts = array_merge($opts, [
                    'cert' => config('postalcode.mtls.cert'),
                    'ssl_key' => config('postalcode.mtls.key'),
                    'verify' => config('postalcode.mtls.ca'),
                ]);
            }

            $client = new Client($opts);
            return new PostcodeService(new ApiResolver($client), (bool)config('postalcode.enabled'));
        });
    }
}
