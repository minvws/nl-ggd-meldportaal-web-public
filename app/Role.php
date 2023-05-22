<?php

declare(strict_types=1);

namespace App;

final class Role
{
    // Users that are able to administer ALL users in the admin portal (cannot login into the meldportaal though)
    public const SUPER_ADMIN = "SUPER_ADMIN";

    // Administrator users, or users that are authenticated with UZI passes as doctors
    public const USER_ADMIN = "USER_ADMIN";

    // Regular users (can not login into the user admin portal)
    public const USER = "USER";

    // Users that are able to use the API
    public const API = "API";

    // Can only create specimen tests
    public const SPECIMEN = "SPECIMEN";

    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::USER_ADMIN,
            self::USER,
            self::API,
            self::SPECIMEN,
        ];
    }
}
