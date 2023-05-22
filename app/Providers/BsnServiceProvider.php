<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Bsn\BsnResolveService;
use App\Services\BsnService;
use App\Services\Monster\FakeMonster;
use App\Services\Monster\Monster;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use MinVWS\Logging\Laravel\LogService;

class BsnServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Nothing to register
    }

    public function boot(): void
    {
        $this->app->singleton(BsnService::class, function () {
            return new BsnService(
                $this->app->get(BsnResolveService::class)
            );
        });

        $this->app->singleton(BsnResolveService::class, function () {
            if (config("monster.fake")) {
                $monsterService = new FakeMonster();
            } else {
                $opts = $this->getMonsterOpts();
                $client = new Client($opts);
                $monsterService = new Monster($client, config('monster.token'));
            }

            return new BsnResolveService(
                $monsterService,
                $this->app->get(LogService::class)
            );
        });
    }

    protected function getMonsterOpts(): array
    {
        $opts = [
            'base_uri' => config('monster.host'),
        ];

        // Add SSL if applicable
        if (config('monster.ssl')) {
            $opts += [
                'ssl_cert' => config('monster.ssl_cert'),
                'ssl_key' => config('monster.ssl_key'),
            ];
        }
        // Set CA cert if client verification is needed
        if (config('monster.ssl_ca_cert')) {
            $opts += [
                'verify' => config('monster.ssl_ca_cert')
            ];
        }

        // string "false" means is false
        if (config('monster.ssl_ca_cert') == "false") {
            $opts += [
                'verify' => false,
            ];
        }

        return $opts;
    }
}
