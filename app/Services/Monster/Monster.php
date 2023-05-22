<?php

declare(strict_types=1);

namespace App\Services\Monster;

use App\Exceptions\AbstractException;
use App\Exceptions\MonsterException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use MinVWS\Crypto\Laravel\Exceptions\CryptoException;

class Monster implements MonsterInterface
{
    private const PATH = '/lookup';

    protected Client $client;
    protected string $token;

    public function __construct(Client $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    /**
     * @throws MonsterException|AbstractException
     */
    public function fetch(array $payload, string $pubKey): array
    {
        if (strlen($pubKey) < 1) {
            throw new CryptoException("Public key must be b64 encoded and not empty");
        }

        $uri = self::PATH;

        // Filter out fields that may or may not be allowed.
        $actualPayload = $this->preparePayload($payload);

        // Add public key we expect monster to encrypt against
        $actualPayload['public_key'] = sodium_bin2hex(base64_decode($pubKey));

        try {
            $response = $this->client->post($uri, [
                'http_errors' => false,
                'verify' => config('monster.ssl_cacert'),
                'json' => $actualPayload,
            ]);
        } catch (GuzzleException $e) {
            throw MonsterException::serverFailure($e);
        }

        $contents = $response->getBody()->getContents();
        try {
            $json = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($json)) {
                return [];
            }
        } catch (\Jsonexception $e) {
            return [];
        }


        return $json;
    }

    public function isHealthy(): bool
    {
        try {
            $this->client->get('/health', ['verify' => config('monster.ssl_cacert')]);
            return true;
        } catch (\Throwable $e) {
            // Fallthrough
        }

        return false;
    }

    public function preparePayload(array $payload): array
    {
        $allowedKeys = [
            'admin_email',
            'bsn',
            'date_of_birth',
            'user_email',
            'admin_email',
            'admin_name',
            'user_name',
            'user_role',
            'postal_code',
        ];

        // Filter out everything that is not set in our allowed keys
        $ret = array_filter($payload, function ($key) use ($allowedKeys) {
            return in_array($key, $allowedKeys);
        }, ARRAY_FILTER_USE_KEY);

        // Make sure that when an allowedKey is not found, it is set with an empty string by default
        $ret = array_merge(array_fill_keys($allowedKeys, ""), $ret);

        $ret['organisation'] = 'Onbekend';
        $ret['access_token'] = $this->token;
        return $ret;
    }
}
