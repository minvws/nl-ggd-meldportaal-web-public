<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Inge7\IdentityHashService;
use App\Services\Inge7\Inge7Service;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\ServiceProvider;
use MinVWS\Crypto\Laravel\Factory;

class Inge7ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IdentityHashService::class, function () {
            return new IdentityHashService(config('inge7.identity_hash_secret'));
        });

        $this->app->singleton(Inge7Service::class, function () {
            $predisConnection = $this->app->get('redis')->connection('inge7');
            if (!$predisConnection instanceof PredisConnection) {
                throw new \RuntimeException('Inge 7 Redis connection is not a PredisConnection');
            }

            return new Inge7Service(
                connection: $this->app->get('redis')->connection('inge7'),
                cryptoService: Factory::createSealboxCryptoService(recipientPubKey: config('inge7.pubkey')),
                identityHashService: $this->app->get(IdentityHashService::class),
                providerIdentifier: config('inge7.provider_identifier'),
                ttl: intval(config('inge7.redis_ttl'))
            );
        });
    }
}
