<?php

declare(strict_types=1);

namespace App\Services\Postcode\Resolver;

use GuzzleHttp\Client;

class ApiResolver implements ResolverInterface
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ResolveException
     */
    public function resolve(string $postcode, int $houseNumber, string $country = 'nl'): array
    {
        try {
            $response = $this->client->post('/check', [
                'http_errors' => false,
                'json' => [
                    'postcode' => $postcode,
                    'house_number' => $houseNumber,
                    'country' => $country,
                ],
            ]);

            if ($response->getStatusCode() === 404) {
                throw new ResolveException('Postcode not found');
            }

            $json = $response->getBody()->getContents();
            $data = json_decode($json, true);
            if ($data === false) {
                throw new ResolveException('Invalid JSON response');
            }
            if (!isset($data['status']) || $data['status'] !== 'success') {
                throw new ResolveException('Invalid status');
            }

            return $data['response'] ?? [];
        } catch (\Exception $e) {
            throw new ResolveException($e->getMessage());
        }
    }
}
