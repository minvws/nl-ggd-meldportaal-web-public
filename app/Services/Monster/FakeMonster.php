<?php

declare(strict_types=1);

namespace App\Services\Monster;

use MinVWS\Crypto\Laravel\Factory;

class FakeMonster implements MonsterInterface
{
    public function fetch(array $payload, string $pubKey): array
    {
        $fields = [
            "firstNames" => "first-name",
            "surname" => "last-name",
            "prefixSurname" => "prefix-surname",
            "location" => "location",
            "gender" => "V",
            "honorary_title" => "PS",
            "postal_code" => "1234AB",
            "infix_partner" => "van",
            "designation_name" => "E",
            "surname_partner" => "Peters",
            "street_name" => "Bananenweg",
            "house_letter" => "A",
            "house_number" => "1",
            "house_number_additional" => "B",
            "info_house_number" => "to",
            "location_information" => "locatie beschrijving",
            "country_address_foreign" => "Duitsland",
            "first_row_foreign_address" => "Platz",
            "second_row_foreign_address" => "10117 Berlin",
            "third_row_foreign_address" => "Platz",
            "date_of_birth" => "19870401"
        ];

        // We need a separate sealbox service, as we are using different keys
        $encryptService = Factory::createSealboxCryptoService("", $pubKey);

        $out = [];
        foreach ($fields as $field => $value) {
            $out[$field] = bin2hex($encryptService->encrypt($value));
        }

        return ["data" => $out, "errors" => []];
    }

    public function isHealthy(): bool
    {
        // Fake is always healthy
        return true;
    }
}
