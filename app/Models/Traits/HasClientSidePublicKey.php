<?php

declare(strict_types=1);

namespace App\Models\Traits;

use RuntimeException;
use SodiumException;

/**
 * Trait HasClientSidePublicKey
 * @package App\Models\Traits
 * @author Pauline Vos <info@pauline-vos.nl>
 */
trait HasClientSidePublicKey
{
    protected static function bootHasClientSidePublicKey(): void
    {
        static::saving(function ($model) {
            $property = $model->getClientPublicKeyProperty();
            $model->$property = self::getClientSidePublicKey();
        });
    }

    protected function getClientPublicKeyProperty(): string
    {
        return 'keyid_client';
    }

    /**
     * @throws SodiumException
     */
    private static function getClientSidePublicKey(): string
    {
        $pubKeyClient = base64_decode(config('crypto.pubkey_client'), true);
        if ($pubKeyClient === false) {
            throw new RuntimeException("EC pubkey invalid");
        }
        return sodium_bin2hex(
            sodium_crypto_generichash($pubKeyClient)
        );
    }
}
