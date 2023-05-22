<?php

declare(strict_types=1);

namespace App\Services;

use PragmaRX\Google2FA\Support\Constants;

/**
 * Class Google2F
 *
 * https://github.com/google/google-authenticator/wiki/Key-Uri-Format
 * Unfortunately Google Authenticator ignores the algorithm parameter.
 *
 * @package App\Providers
 */
class Google2FA extends \PragmaRX\Google2FA\Google2FA
{
    /**
     * Algorithm.
     *
     * @var string
     */
    protected $algorithm = Constants::SHA1;

    /**
     * Length of the Token generated.
     *
     * @var int
     */
    protected $oneTimePasswordLength = 6;

    /**
     * Interval between key regeneration.
     *
     * @var int
     */
    protected $keyRegeneration = 30;
}
