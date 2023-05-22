<?php

declare(strict_types=1);

namespace Tests\Feature\Service;

use App\Models\Test;
use App\Models\User;
use App\Services\Bsn\BsnResolveService;
use App\Services\BsnService;
use App\Services\MapTestBrandsToEuValuesService;
use App\Services\Monster\FakeMonster;
use App\Services\TestService;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SealboxCryptoInterface;
use MinVWS\Logging\Laravel\LogService;
use SodiumException;
use Tests\Feature\TestCase;

class TestServiceTest extends TestCase
{
    public function testTestIsStored(): void
    {
        // Create test service with fake monster
        $service = $this->createTestService();

        // Create data that we will send to the service
        $data = Test::factory([
            'bsn' => '123456789',
            'initials' => 'B',
            'insertion' => 'de',
            'surname' => 'Bouwer',
            'birthdate' => '1990-01-01',
        ])->raw();

        // Store the test
        $service->storeTest($data, $this->getUser());

        // Assert that the test is stored in the database
        $test = Test::first();
        $this->assertNotNull($test);
        $this->assertSame('123456789', $test->bsn);
        $this->assertSame('B', $test->initials);
        $this->assertSame('de', $test->insertion);
        $this->assertSame('1990-01-01', $test->birthdate);
        $this->assertFalse($test->is_specimen);

        // Assert that the surname is replaced with surname from monster
        $this->assertSame('last-name', $test->surname);
    }

    public function testTestIsEnrichedWithMonsterInfo(): void
    {
        // Create test service with fake monster
        $service = $this->createTestService();

        // Create data that we will send to the service
        $data = Test::factory([
            'bsn' => '123456789',
            'initials' => 'B',
            'insertion' => 'de',
            'surname' => 'Bouwer',
            'birthdate' => '1990-01-01',
        ])->raw();

        // Store the test
        $service->storeTest($data, $this->getUser(), specimen: true);

        // Assert that the test is stored in the database
        $test = Test::first();
        $this->assertNotNull($test);
        $this->assertSame('first-name', $test->brp_first_names);
        $this->assertSame('prefix-surname', $test->brp_prefix_surname);
        $this->assertSame('last-name', $test->brp_surname);
        $this->assertSame('19870401', $test->brp_date_of_birth);
        $this->assertTrue($test->is_specimen);
    }

    /**
     * @dataProvider provideTestIsEnrichedWithEuValues
     * @param $brandUsedTest
     * @param $euEventType
     * @param $euEventManufacturer
     * @param $euEventName
     * @return void
     */
    public function testTestIsEnrichedWithEuValues(
        $brandUsedTest,
        $euEventType,
        $euEventManufacturer,
        $euEventName
    ): void {
        // Create test service with fake monster
        $service = $this->createTestService();

        // Create data that we will send to the service
        $data = Test::factory([
            'bsn' => '123456789',
            'initials' => 'B',
            'insertion' => 'de',
            'surname' => 'Bouwer',
            'birthdate' => '1990-01-01',
            'brand_used_test' => $brandUsedTest
        ])->raw();

        // Store the test
        $service->storeTest($data, $this->getUser());

        // Assert that the test is stored in the database
        $test = Test::first();
        $this->assertNotNull($test);
        $this->assertSame($euEventType, $test->eu_event_type);
        $this->assertSame($euEventManufacturer, $test->eu_event_manufacturer);
        $this->assertSame($euEventName, $test->eu_event_name);
    }

    public function provideTestIsEnrichedWithEuValues(): array
    {
        return [
            [
                'A',
                'LP217198-3',
                '1232',
                'Panbio COVID-19 Ag Test',
            ],
            [
                'C',
                'LP6464-4',
                null,
                'SARS-CoV-2 Polymerase Chain Reaction (PCR)',
            ],
            [
                'X',
                null,
                null,
                null,
            ],
        ];
    }

    public function testTestIsNotCreatedWhenMissingBsnPostcodeOrBirthdate(): void
    {
        $this->markTestIncomplete('Test needs to be written');
    }

    /**
     * @throws SodiumException
     */
    public function getSealboxCryptoService(): SealboxCryptoInterface
    {
        [$publicKey, $secretKey] = $this->generateSodiumKeys();

        $crypto = Factory::createSealboxCryptoService(
            privKey: $secretKey,
            recipientPubKey: $publicKey,
        );

        return $crypto;
    }

    /**
     * Generates sodium keys and returns public and secret key.
     * @returns array<string, string>
     * @throws SodiumException
     */
    protected function generateSodiumKeys(): array
    {
        $keypair = sodium_crypto_box_keypair();

        return [
            base64_encode(sodium_crypto_box_publickey($keypair)),
            base64_encode(sodium_crypto_box_secretkey($keypair)),
        ];
    }

    /**
     * Creates a test service with fake monster.
     * @return TestService
     */
    protected function createTestService(): TestService
    {
        return new TestService(
            bsnService: new BsnService(
                resolver: new BsnResolveService(
                    monster: new FakeMonster(),
                    logService: new LogService(),
                ),
            ),
            euValuesMappingService: new MapTestBrandsToEuValuesService(),
            logService: new LogService(),
        );
    }

    protected function getUser(): User
    {
        $user = new User();
        $user->id = 12345;
        $user->email = '12345@uzi.ura';

        return $user;
    }
}
