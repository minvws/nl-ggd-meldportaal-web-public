<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\MapTestBrandsToEuValuesService;
use Tests\Unit\TestCase;

class MapTestBrandsToEuValuesServiceTest extends TestCase
{
    /**
     * @dataProvider provideMappingValues
     * @param $brandUsedTest
     * @param $expectedMapping
     * @return void
     */
    public function testMapping($brandUsedTest, $expectedMapping): void
    {
        $service = new MapTestBrandsToEuValuesService();

        $mapping = $service->getEuEventValuesForTestBrand($brandUsedTest);

        $this->assertSame($expectedMapping, $mapping);
    }

    public function provideMappingValues(): array
    {
        return [
            [
                'A',
                [
                    'eu_event_type' => 'LP217198-3',
                    'eu_event_manufacturer' => '1232',
                    'eu_event_name' => 'Panbio COVID-19 Ag Test',
                ]
            ],
            [
                'E',
                [
                    'eu_event_type' => 'LP217198-3',
                    'eu_event_manufacturer' => '',
                    'eu_event_name' => '',
                ]
            ],
            [
                'C',
                [
                    'eu_event_type' => 'LP6464-4',
                    'eu_event_manufacturer' => null,
                    'eu_event_name' => 'SARS-CoV-2 Polymerase Chain Reaction (PCR)',
                ]
            ],
            [
                'X',
                [
                    'eu_event_type' => null,
                    'eu_event_manufacturer' => null,
                    'eu_event_name' => null,
                ]
            ],
            [
                '',
                [
                    'eu_event_type' => null,
                    'eu_event_manufacturer' => null,
                    'eu_event_name' => null,
                ]
            ],
        ];
    }
}
