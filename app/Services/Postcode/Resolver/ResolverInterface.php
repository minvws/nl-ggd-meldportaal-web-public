<?php

declare(strict_types=1);

namespace App\Services\Postcode\Resolver;

interface ResolverInterface
{
    public function resolve(string $postcode, int $houseNumber, string $country = 'nl'): array;
}
