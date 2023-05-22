<?php

declare(strict_types=1);

namespace App\Services\Monster;

interface MonsterInterface
{
    public function isHealthy(): bool;
    public function fetch(array $payload, string $pubKey): array;
}
