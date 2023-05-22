<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

return [
    'guard' => 'web',
    'passwords' => 'users',

    'username' => 'email',
    'email' => 'email',

    'home' => '/',

    'prefix' => '',
    'domain' => null,

    'middleware' => ['web'],

    'limiters' => [
//        'login' => 'login',
//        'two-factor' => 'two-factor',
    ],

    'views' => true,

    'features' => [
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirmPassword' => true,
            'recovery' => false,
        ]),
    ],

];
