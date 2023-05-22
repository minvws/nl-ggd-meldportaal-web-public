<?php

declare(strict_types=1);

namespace App\Services\Postcode;

use App\Services\Postcode\Resolver\ResolverInterface;

class PostcodeService
{
    protected ResolverInterface $resolver;
    protected bool $enabled;

    public function __construct(ResolverInterface $resolver, bool $enabled = true)
    {
        $this->resolver = $resolver;
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function resolve(string $postcode, int $houseNumber, string $country = 'nl'): ?array
    {
        if (! $this->enabled) {
            return null;
        }

        try {
            return $this->resolver->resolve($postcode, $houseNumber, $country);
        } catch (\Exception) {
            // fallthrough
        }

        return null;
    }
}
