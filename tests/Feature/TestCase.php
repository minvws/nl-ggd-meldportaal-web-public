<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use MinVWS\Logging\Laravel\Loggers\DbLogger;
use MinVWS\Logging\Laravel\LogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SodiumException;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * @throws SodiumException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setCryptoKeysForDatabaseSavingAndReading();
    }

    /**
     * @param array $attributes
     * @param bool $own
     *
     * @return User
     */
    protected function setupUser(array $attributes = [], bool $own = false): User
    {
        /** @var User $user */
        $user = User::factory()->create($attributes);
        $user->password_updated_at = now();
        $user->active = true;
        if ($own) {
            $this->be($user);
        }
        return $user;
    }

    protected function setupLogService(): LogService
    {
        return new LogService([
            new DbLogger(AuditLog::class),
        ]);
    }

    /**
     * @throws SodiumException
     */
    protected function setCryptoKeysForDatabaseSavingAndReading(): void
    {
        // Set config for crypto service to encrypt/decrypt the db data
        [$publicKey, $secretKey] = $this->generateSodiumKeys();

        config()->set('crypto.database.public_key', $publicKey);
        config()->set('crypto.database.private_key', $secretKey);
    }

    /**
     * Generates sodium keys and returns public and secret key.
     * @returns array<string, string>
     * @throws SodiumException
     */
    protected function generateSodiumKeys(): array
    {
        $keypair = sodium_crypto_box_keypair();

        return [
            base64_encode(sodium_crypto_box_publickey($keypair)),
            base64_encode(sodium_crypto_box_secretkey($keypair)),
        ];
    }
}
