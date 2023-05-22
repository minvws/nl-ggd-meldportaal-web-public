<?php

declare(strict_types=1);

namespace App\Services\Postcode\Resolver;

class MockResolver implements ResolverInterface
{
    protected array $mocks = [
        'nl' => [
            '1234AB' => [
                'postcode' => '1234AB',
                'street' => 'Burgemeester Jhr. Quarles van Uffordlaan',
                'city' => 'Apeldoorn',
                'country' => 'nl',
            ],
            '1111AA' => [
                'postcode' => '1111AA',
                'street' => 'Kalverstraat',
                'city' => 'Amsterdam',
                'country' => 'nl',
            ],
        ],
        'be' => [
            '1111' => [
                'postcode' => '1111',
                'street' => '',
                'city' => 'Brussels',
                'country' => 'be',
            ]
        ]
    ];

    /**
     * @throws ResolveException
     */
    public function resolve(string $postcode, int $houseNumber, string $country = 'nl'): array
    {
        if (isset($this->mocks[$country][$postcode])) {
            return $this->mocks[$country][$postcode];
        }

        throw new ResolveException('Postcode not found');
    }
}
