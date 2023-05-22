<?php

declare(strict_types=1);

namespace App\Services\Inge7\Data;

class PositiveTestEventInformation extends EventInformation
{
    public function __construct(
        protected string $uniqueIdentifier,
        protected bool $isSpecimen,
        protected string $sampleDate,
        protected bool $positiveResult,
        protected string $facility,
        protected string $type,
        protected string $name = '',
        protected ?string $manufacturer = null,
        protected ?string $country = null,
    ) {
        $positiveTest = [
            "sampleDate" => $this->sampleDate,
            "positiveResult" => $this->positiveResult,
            "facility" => $this->facility,
            "type" => $this->type,
            "name" => $this->name,
            "manufacturer" => $this->manufacturer,
        ];

        if ($this->country !== null) {
            $positiveTest["country"] = $this->country;
        }

        parent::__construct(
            "positivetest",
            $uniqueIdentifier,
            $isSpecimen,
            $positiveTest
        );
    }
}
