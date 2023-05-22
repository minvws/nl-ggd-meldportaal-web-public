<?php

declare(strict_types=1);

namespace App\Services\Inge7\Data;

use Stringable;

class EventInformation implements Stringable
{
    public function __construct(
        protected string $type,
        protected string $uniqueIdentifier,
        protected bool $isSpecimen,
        protected array $eventData,
    ) {
    }

    public function toArray(): array
    {
        return [
            "type" => $this->type,
            "unique" => $this->uniqueIdentifier,
            "isSpecimen" => $this->isSpecimen,
            $this->type => $this->eventData
        ];
    }

    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
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
}
