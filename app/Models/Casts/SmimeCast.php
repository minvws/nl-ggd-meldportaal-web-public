<?php

declare(strict_types=1);

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SealboxCryptoInterface;

/**
 * Class SmimeCast
 *
 * Casts given value to an smime encrypted and base64 encoded value. This CANNOT be decrypted by our system
 *
 * @author jthijssen@noxlogic.nl
 */
class SmimeCast implements CastsInboundAttributes
{
    /** @var SealboxCryptoInterface */
    protected $smimeService;

    /**
     * SmimeCast constructor.
     */
    public function __construct()
    {
        // We cannot use constructor DI as these cast classes are not regularly constructed.
        $this->smimeService = Factory::createSealboxCryptoService(
            config('crypto.sealbox.private_key'),
            config('crypto.sealbox.public_key')
        );
    }

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed|string
     */
    public function set($model, $key, $value, $attributes)
    {
        return base64_encode($this->smimeService->encrypt($value));
    }
}
