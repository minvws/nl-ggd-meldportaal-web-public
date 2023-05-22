<?php

declare(strict_types=1);

namespace App\Services;

/**
 * For Inge 7 we need to map the test brand to the EU values
 * The mapping below is based on the following document:
 * @link https://github.com/minvws/nl-ggd-meldportaal-web/issues/72
 */
class MapTestBrandsToEuValuesService
{
    protected const EU_EVENT_TYPE_RAT = 'LP217198-3';
    protected const EU_EVENT_TYPE_NAAT = 'LP6464-4';

    /**
     * Returns eu values based on the test brand
     * @param string $value A value like 'A', 'B', 'D', 'P', 'N', 'Q', 'R', 'S'
     * @return array{eu_event_type: string|null, eu_event_manufacturer: string|null, eu_event_name: string|null}
     */
    public function getEuEventValuesForTestBrand(string $value): array
    {
        return match (strtoupper($value)) {
            // RAT TESTS
            'A' => [
                'eu_event_type' => self::EU_EVENT_TYPE_RAT,
                'eu_event_manufacturer' => '1232',
                'eu_event_name' => 'Panbio COVID-19 Ag Test',
            ],
            'B' => [
                'eu_event_type' => self::EU_EVENT_TYPE_RAT,
                'eu_event_manufacturer' => '1065',
                'eu_event_name' => 'BD Veritor System for Rapid Detection of SARS-CoV-2',
            ],
            'D' => [
                'eu_event_type' => self::EU_EVENT_TYPE_RAT,
                'eu_event_manufacturer' => '1960',
                'eu_event_name' => 'LIAISON\u00ae SARS-CoV-2 Ag',
            ],
            'N' => [
                'eu_event_type' => self::EU_EVENT_TYPE_RAT,
                'eu_event_manufacturer' => '1244',
                'eu_event_name' => 'GenBody COVID-19 Ag',
            ],
            'Q' => [
                'eu_event_type' => self::EU_EVENT_TYPE_RAT,
                'eu_event_manufacturer' => '1097',
                'eu_event_name' => 'Sofia SARS Antigen FIA',
            ],
            'R' => [
                'eu_event_type' => self::EU_EVENT_TYPE_RAT,
                'eu_event_manufacturer' => '345',
                'eu_event_name' => 'STANDARD Q COVID-19 Ag Test',
            ],
            'S' => [
                'eu_event_type' => self::EU_EVENT_TYPE_RAT,
                'eu_event_manufacturer' => '344',
                'eu_event_name' => 'STANDARD F COVID-19 Ag FIA',
            ],

            // RAT TESTS (OTHER)
            'E', 'F', 'G', 'H', 'J', 'K', 'T', 'U', 'W', 'P' => [
                'eu_event_type' => self::EU_EVENT_TYPE_RAT,
                'eu_event_manufacturer' => '',
                'eu_event_name' => '',
            ],

            // NAAT TESTS
            'C', 'M' => [
                'eu_event_type' => self::EU_EVENT_TYPE_NAAT,
                'eu_event_manufacturer' => null,
                'eu_event_name' => 'SARS-CoV-2 Polymerase Chain Reaction (PCR)',
            ],
            'L' => [
                'eu_event_type' => self::EU_EVENT_TYPE_NAAT,
                'eu_event_manufacturer' => null,
                'eu_event_name' => 'SARS-CoV-2 Loop-Mediated Isothermal Amplification (LAMP)',
            ],

            // NO EU VALUES
            default => [
                'eu_event_type' => null,
                'eu_event_manufacturer' => null,
                'eu_event_name' => null,
            ],
        };
    }
}
