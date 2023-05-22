<?php

declare(strict_types=1);

namespace App\Services\Inge7;

/**
 * Service that generates an identity hash based on the specifications.
 *
 * @link https://github.com/minvws/nl-covid19-coronacheck-provider-docs/blob/main/docs/providing-events-by-digid.md#identity-hash
 */
class IdentityHashService
{
    public function __construct(
        protected string $secret,
    ) {
    }

    /**
     * Generates an identity hash.
     * The First Name and Birth Name needs to be UTF-8 encoded,
     * including any diacritics, full length, as it appears in the BRP.
     *
     * @param string $bsn BSN
     * @param string $firstName First Name
     * @param string $lastName Birth Name
     * @param string $birthDate Birthdate in the format YYYYMMDD
     * @return string
     */
    public function getIdentityHash(string $bsn, string $firstName, string $lastName, string $birthDate): string
    {
        return hash_hmac(
            "sha256",
            $bsn . "-" . $firstName . "-" . $lastName . "-" . substr($birthDate, 6),
            $this->secret
        );
    }
}
