<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Google2FA;

/**
 * Class TwoFactorAuthenticationProvider.
 * Uses local Google2FA provider to override settings.
 *
 * @package App\Providers
 * @author annejan@noprotocol.nl
 */
class TwoFactorAuthenticationProvider extends \Laravel\Fortify\TwoFactorAuthenticationProvider
{
    /**
     * Create a new two factor authentication provider instance.
     *
     * @param  Google2FA  $engine
     * @return void
     */
    public function __construct(Google2FA $engine)
    {
        parent::__construct($engine);
    }
}
