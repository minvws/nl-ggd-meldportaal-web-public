<?php

declare(strict_types=1);

return [
    'enabled' => env('POSTAL_CODE_API_ENABLED', true),
    'mock' => env('POSTAL_CODE_API_MOCK_ENABLED', false),
    'url' => env('POSTAL_CODE_API_URL', 'https://127.0.0.1'),
    'verify_ssl' => env('POSTAL_CODE_API_VERIFY_SSL', false),
    'mtls' => [
        'enabled' => env('POSTAL_CODE_API_MTLS_ENABLED', false),
        'cert' => env('POSTAL_CODE_API_CERT', ''),
        'key' => env('POSTAL_CODE_API_KEY', ''),
        'ca' => env('POSTAL_CODE_API_CA', ''),
    ],
];
