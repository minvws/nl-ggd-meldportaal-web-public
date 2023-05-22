<?php

declare(strict_types=1);

return [
    'host' => env('MONSTER_HOST', ''),
    'token' => env('MONSTER_TOKEN', ''),
    'ssl' => env('MONSTER_SSL_ENABLED', true),
    'ssl_cert' => env('MONSTER_SSL_CERT', ''),
    'ssl_key' => env('MONSTER_SSL_KEY', ''),
    'ssl_cacert' => env('MONSTER_SSL_CA_CERT', ''),
    'fake' => env('FAKE_MONSTER', false),
    'pubkey' => env('MONSTER_DATA_ENCRYPTION_PUB', '')
];
