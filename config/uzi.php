<?php

declare(strict_types=1);

use MinVWS\PUZI\UziConstants;

return [
    // True if the CA must be checked on the x509 certificate, set to false for using test cards
    'strict_ca_check' => env('UZI_STRICT_CA_CHECK', true),

    // Which card types are allowed to log in
    'allowed_types' => [
        UziConstants::UZI_TYPE_CARE_PROVIDER,
    ],

    // Which roles are allowed to log in
    'allowed_roles' => [
        UziConstants::UZI_ROLE_DOCTOR,
    ],

    // The CA certificates to use for validating the UZI certificate. Must be concatenated in a single file.
    'ca_certs_path' => env('UZI_CA_CERTS_PATH', null),

    'override_development_cert' => env('OVERRIDE_UZI_CERT', false),

    'login_url' => env('UZI_LOGIN_URL', ''),

    'enabled' => env('FEATURE_AUTH_UZI', false),
];
