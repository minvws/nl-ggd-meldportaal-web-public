<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SealboxCryptoInterface;
use SodiumException;

/**
 * Trait AsymEncrypted
 *
 * Encrypts and/or decrypts values with our crypto.database.* keypair. This is used for
 * one-way store and load from the database.
 */
trait AsymEncrypted
{
    /**
     * Decrypt required data, checking for changed crypto keys.
     *
     * @param string $key
     * @return mixed
     * @throws SodiumException|DecryptException|Exception
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (! in_array($key, $this->encrypted)) {
            return $value;
        }

        if (!$value) {
            return $value;
        }

        /** @var string|false $decoded */
        $decoded = base64_decode($value);
        if ($decoded === false) {
            throw new DecryptException("Could not decode attribute $key");
        }

        $sealbox = $this->getSealbox();
        try {
            return $sealbox->decrypt($decoded);
        } catch (Exception $e) {
            throw new DecryptException("Could not decrypt attribute $key");
        }
    }

    /**
     * Store given data encrypted if part of the encrypted fields,
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @throws SodiumException
     * @throws Exception
     */
    public function setAttribute($key, $value)
    {
        if (is_null($value)) {
            return parent::setAttribute($key, null);
        }

        if (in_array($key, $this->encrypted)) {
            $sealbox = $this->getSealbox();
            $value = base64_encode($sealbox->encrypt(strval($value)));
        }

        return parent::setAttribute($key, $value);
    }

    private function getSealbox(): SealboxCryptoInterface
    {
        return Factory::createSealboxCryptoService(
            privKey: config('crypto.database.private_key'),
            recipientPubKey: config('crypto.database.public_key')
        );
    }
}
