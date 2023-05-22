<?php

declare(strict_types=1);

return [

    /**
     * Provider identifier for the events.
     */
    'provider_identifier' => env('INGE7_PROVIDER_IDENTIFIER', 'XXX'),

    /**
     * Shared secret for generating the identity hash.
     * This is used to generate the identity hash to be used as part of the keys in Redis.
     */
    'identity_hash_secret' => env('INGE7_IDENTITY_HASH_SECRET', ''),

    /**
     * Sodium public key of inge 7.
     * This is used to encrypt the events and holder information before adding it to Redis.
     */
    'pubkey' => env('INGE7_PUBKEY', ''),

    /**
     * TTL for the events in Redis.
     * This is used to set the TTL for the events in Redis.
     */
    'redis_ttl' => env('INGE7_REDIS_TTL', 24 * 60 * 60),

];
