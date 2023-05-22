<?php

declare(strict_types=1);

return [

    'host' => env('GGD_HOST', ''),
    'credentials' => [
        'client' => env('GGD_CREDENTIALS_CLIENT', ''),
        'secret' => env('GGD_CREDENTIALS_SECRET', ''),
    ],
];
