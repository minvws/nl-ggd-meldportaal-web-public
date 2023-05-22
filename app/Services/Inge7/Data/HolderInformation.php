<?php

declare(strict_types=1);

namespace App\Services\Inge7\Data;

use Stringable;

class HolderInformation implements Stringable
{
    /**
     * @param string $providerIdentifier
     * @param string $bsn
     * @param string $firstName
     * @param string|null $infix
     * @param string $lastName
     * @param string $birthDate Format: YYYYMMDD
     */
    public function __construct(
        protected string $providerIdentifier,
        protected string $bsn,
        protected string $firstName,
        protected ?string $infix,
        protected string $lastName,
        protected string $birthDate,
    ) {
    }

    public function toArray(): array
    {
        return [
            "protocolVersion" => "3.0",
            "providerIdentifier" => $this->providerIdentifier,
            "status" => "complete",
            "holder" => [
                "bsn" => $this->bsn,
                "firstName" => $this->firstName,
                "infix" => $this->infix,
                "lastName" => $this->lastName,
                "birthDate" => $this->convertBirthDate($this->birthDate),
            ]
        ];
    }

    public function __toString(): string
    {
        try {
            return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            // TODO: Throw new Inge7Exception
            return '';
        }
    }

    /**
     * Converts the birthdate from YYYYMMDD to the format YYYY-MM-DD.
     * @param string $birthDate Format: YYYYMMDD
     * @return string Format: YYYY-MM-DD
     */
    protected function convertBirthDate(string $birthDate): string
    {
        return substr($this->birthDate, 0, 4)
            . '-' . substr($this->birthDate, 4, 2)
            . '-' . substr($this->birthDate, 6, 2);
    }
}
