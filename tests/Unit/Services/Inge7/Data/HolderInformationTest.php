<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Inge7\Data;

use App\Services\Inge7\Data\HolderInformation;
use JsonException;
use Tests\Unit\TestCase;

class HolderInformationTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testHolderInformation(): void
    {
        $positiveTestEvent = new HolderInformation(
            providerIdentifier: 'XXX',
            bsn: '123456789',
            firstName: 'John',
            infix: 'van',
            lastName: 'Doe',
            birthDate: '20210101',
        );

        $expected = [
            "protocolVersion" => "3.0",
            "providerIdentifier" => 'XXX',
            "status" => "complete",
            "holder" => [
                "bsn" => '123456789',
                "firstName" => 'John',
                "infix" => 'van',
                "lastName" => 'Doe',
                "birthDate" => '2021-01-01',
            ]
        ];

        $this->assertEquals($expected, $positiveTestEvent->toArray());
        $this->assertEquals(json_encode($expected, JSON_THROW_ON_ERROR), $positiveTestEvent->__toString());
    }
}
