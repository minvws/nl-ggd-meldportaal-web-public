<?php

declare(strict_types=1);

return [
    # Keypair for encryption between the frontend form and the backend
    'backend' => [
        'public_key' => env('BACKEND_PUBLIC_KEY'),
        'private_key' => env('BACKEND_PRIVATE_KEY'),
    ],

    # Keypair for encryption in the database. Public key is used by the
    # frontend/form for encryption. Private key part is used for decryption
    # in the app:test:sync command, which runs on a separate machine.
    'database' => [
        'public_key' => env('DATABASE_PUBLIC_KEY'),
        'private_key' => env('DATABASE_PRIVATE_KEY'),
    ],

    # Public key for encryption to inge7. Public key is used by the
    # inge7 service for encryption. inge 7 will load the data from
    # redis and will decrypt the data with its private key.
    'inge7' => [
        'public_key' => env('INGE7_PUBLIC_KEY'),
    ]
];
