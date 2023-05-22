<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Inge7\Data;

use App\Services\Inge7\Data\PositiveTestEventInformation;
use JsonException;
use Tests\Unit\TestCase;

class PositiveTestEventInformationTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testPositiveTestEventInformation(): void
    {
        $positiveTestEvent = new PositiveTestEventInformation(
            uniqueIdentifier: '1',
            isSpecimen: true,
            sampleDate: '2021-01-01',
            positiveResult: true,
            facility: 'A',
            type: 'B',
            name: 'C',
            manufacturer: 'D',
            country: 'E',
        );

        $expected = [
            "type" => 'positivetest',
            "unique" => '1',
            "isSpecimen" => true,
            "positivetest" => [
                "sampleDate" => '2021-01-01',
                "positiveResult" => true,
                "facility" => 'A',
                "type" => 'B',
                "name" => 'C',
                "manufacturer" => 'D',
                "country" => 'E',
            ]
        ];

        $this->assertEquals($expected, $positiveTestEvent->toArray());
        $this->assertEquals(json_encode($expected, JSON_THROW_ON_ERROR), $positiveTestEvent->__toString());
    }
}
