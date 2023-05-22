<?php

declare(strict_types=1);

namespace Tests\Feature\Service\Inge7;

use App\Models\Test;
use App\Services\Inge7\IdentityHashService;
use App\Services\Inge7\Inge7Service;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Redis\Connections\PredisConnection;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SealboxCryptoInterface;
use Mockery;
use SodiumException;
use Tests\Feature\TestCase;

class Inge7ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testTestIsNotSynchronisedWhenEuTypeIsNull(): void
    {
        // Create Test
        $test = Test::factory()
            ->withEuValues('X')
            ->create([
                'bsn' => '123456789',
                'initials' => 'B',
                'insertion' => 'de',
                'surname' => 'Bouwer',
                'birthdate' => '1990-01-01',
                'brp_first_names' => 'Bob',
                'brp_prefix_surname' => 'de',
                'brp_surname' => 'Bouwer',
                'brp_date_of_birth' => '19900101',
            ]);

        // Mocks
        $mockedIdentityHashService = Mockery::mock(IdentityHashService::class);
        $mockedIdentityHashService
            ->shouldReceive('getIdentityHash')
            ->andReturn('some-identity-hash-string');

        // Assertions
        $mockedRedisConnection = Mockery::mock(PredisConnection::class);
        $mockedRedisConnection->shouldNotReceive('set');
        $mockedRedisConnection->shouldNotReceive('transaction');
        $mockedRedisConnection->shouldNotReceive('sismember');
        $mockedRedisConnection->shouldNotReceive('sadd');

        // Create service and set test
        $service = new Inge7Service(
            connection: $mockedRedisConnection,
            cryptoService: $this->getSealboxCryptoService(),
            identityHashService: $mockedIdentityHashService,
            providerIdentifier: 'XXX',
        );

        $service->setTest($test);
    }

    public function testHolderInformationToRedisWithMockedIdentityHashService(): void
    {
        $crypto = $this->getSealboxCryptoService();

        // Create Test
        $test = Test::factory()
            ->withEuValues('A')
            ->create([
                'bsn' => '123456789',
                'initials' => 'B',
                'insertion' => 'de',
                'surname' => 'Bouwer',
                'birthdate' => '1990-01-01',
                'brp_first_names' => 'Bob',
                'brp_prefix_surname' => 'de',
                'brp_surname' => 'Bouwer',
                'brp_date_of_birth' => '19900101',
            ]);

        // Mocks
        $mockedIdentityHashService = Mockery::mock(IdentityHashService::class);
        $mockedIdentityHashService
            ->shouldReceive('getIdentityHash')
            ->andReturn('some-identity-hash-string');

        $mockedRedisConnection = Mockery::mock(PredisConnection::class);
        $mockedRedisConnection
            ->shouldReceive('set')
            ->once()
            ->withArgs([
                'some-identity-hash-string:holder',
                Mockery::capture($encryptedHolderInformation),
                'EX',
                86400
            ]);

        $mockedRedisConnection
            ->shouldReceive('transaction')
            ->once()
            ->withArgs(function (Closure $closure) {
                $closure();
                return true;
            });

        $mockedRedisConnection
            ->shouldReceive('sismember')
            ->once()
            ->withArgs(['some-identity-hash-string:digests', hash('sha256', $test->id)])
            ->andReturn(false);

        $mockedRedisConnection
            ->shouldReceive('sadd')
            ->once()
            ->withArgs(['some-identity-hash-string:events', Mockery::capture($encryptedEventInformation)]);
        $mockedRedisConnection
            ->shouldReceive('sadd')
            ->once()
            ->withArgs(['some-identity-hash-string:digests', Mockery::capture($digestsInformation)]);
        $mockedRedisConnection
            ->shouldReceive('expire')
            ->twice();

        $service = new Inge7Service(
            connection: $mockedRedisConnection,
            cryptoService: $crypto,
            identityHashService: $mockedIdentityHashService,
            providerIdentifier: 'XXX',
        );

        $service->setTest($test);

        $this->assertNotNull($encryptedHolderInformation, 'Redis did not receive holder information');
        $this->assertNotNull($encryptedEventInformation, 'Redis did not receive event information');
        $this->assertNotNull($digestsInformation, 'Redis did not receive digest information');

        $holderInformation = json_decode($crypto->decrypt(base64_decode($encryptedHolderInformation)), true);
        $this->assertSame([
            "protocolVersion" => "3.0",
            "providerIdentifier" => "XXX",
            "status" => "complete",
            "holder" => [
                "bsn" => '123456789',
                "firstName" => 'Bob',
                "infix" => 'de',
                "lastName" => 'Bouwer',
                "birthDate" => '1990-01-01',
            ]
        ], $holderInformation);
    }

    /**
     * @throws SodiumException
     */
    public function testHolderInformationToRedis(): void
    {
        // Create test based on fixed data
        $test = Test::factory()
            ->withEuValues('A')
            ->create([
                'bsn' => '123456789',
                'initials' => 'B',
                'insertion' => 'de',
                'surname' => 'Bouwer',
                'birthdate' => '1990-01-01',
                'brp_first_names' => 'Bob',
                'brp_prefix_surname' => 'de',
                'brp_surname' => 'Bouwer',
                'brp_date_of_birth' => '19900101',
            ]);

        // Create identity hash based on fixed data
        $identityHashService = new IdentityHashService('some-secret');
        $identityHash = $identityHashService->getIdentityHash(
            bsn: '123456789',
            firstName: 'Bob',
            lastName: 'Bouwer',
            birthDate: '19900101',
        );

        // Initialise mocks
        $crypto = $this->getSealboxCryptoService();

        $mockedRedisConnection = Mockery::mock(PredisConnection::class);
        $mockedRedisConnection
            ->shouldReceive('set')
            ->once()
            ->withArgs([
                Mockery::capture($redisHolderKey),
                Mockery::capture($redisEncryptedHolderInformation),
                'EX',
                86400,
            ]);

        $mockedRedisConnection
            ->shouldReceive('transaction')
            ->once()
            ->withArgs(function (Closure $closure) {
                $closure();
                return true;
            });
        $mockedRedisConnection
            ->shouldReceive('sismember')
            ->once()
            ->withArgs([$identityHash . ':digests', hash('sha256', $test->id)])
            ->andReturn(false);
        $mockedRedisConnection
            ->shouldReceive('sAdd')
            ->once()
            ->withArgs([$identityHash . ':events', Mockery::capture($redisEncryptedEventInformation)]);
        $mockedRedisConnection
            ->shouldReceive('sAdd')
            ->once()
            ->withArgs([$identityHash . ':digests', Mockery::capture($redisDigestsInformation)]);
        $mockedRedisConnection
            ->shouldReceive('expire')
            ->twice();

        $service = new Inge7Service(
            connection: $mockedRedisConnection,
            cryptoService: $crypto,
            identityHashService: $identityHashService,
            providerIdentifier: 'XXX',
        );

        // Send test to redis
        $service->setTest($test);

        // Assert redis keys
        $this->assertSame("$identityHash:holder", $redisHolderKey);

        $this->assertNotNull($redisEncryptedHolderInformation, 'Redis did not receive holder information');
        $this->assertNotNull($redisEncryptedEventInformation, 'Redis did not receive event information');
        $this->assertNotNull($redisDigestsInformation, 'Redis did not receive digest information');

        // Assert holder information
        $holderInformation = json_decode($crypto->decrypt(base64_decode($redisEncryptedHolderInformation)), true);
        $this->assertSame([
            "protocolVersion" => "3.0",
            "providerIdentifier" => "XXX",
            "status" => "complete",
            "holder" => [
                "bsn" => '123456789',
                "firstName" => 'Bob',
                "infix" => 'de',
                "lastName" => 'Bouwer',
                "birthDate" => '1990-01-01',
            ]
        ], $holderInformation);
    }

    /**
     * @throws SodiumException
     */
    public function testTestEventIsNotAddedWhenEventExists(): void
    {
        // Create test based on fixed data
        $test = Test::factory()
            ->withEuValues('A')
            ->create([
                'bsn' => '123456789',
                'initials' => 'B',
                'insertion' => 'de',
                'surname' => 'Bouwer',
                'birthdate' => '1990-01-01',
                'brp_first_names' => 'Bob',
                'brp_prefix_surname' => 'de',
                'brp_surname' => 'Bouwer',
                'brp_date_of_birth' => '19900101',
            ]);

        // Create identity hash based on fixed data
        $identityHashService = new IdentityHashService('some-secret');
        $identityHash = $identityHashService->getIdentityHash(
            bsn: '123456789',
            firstName: 'Bob',
            lastName: 'Bouwer',
            birthDate: '19900101',
        );

        // Initialise mocks
        $crypto = $this->getSealboxCryptoService();

        $mockedRedisConnection = Mockery::mock(PredisConnection::class);
        $mockedRedisConnection
            ->shouldReceive('set')
            ->once()
            ->withArgs([
                Mockery::capture($redisHolderKey),
                Mockery::capture($redisEncryptedHolderInformation),
                'EX',
                86400,
            ]);

        $mockedRedisConnection
            ->shouldReceive('transaction')
            ->once()
            ->withArgs(function (Closure $closure) {
                $closure();
                return true;
            });
        $mockedRedisConnection
            ->shouldReceive('sismember')
            ->once()
            ->withArgs([$identityHash . ':digests', hash('sha256', $test->id)])
            ->andReturn(true);
        $mockedRedisConnection->shouldNotReceive('sAdd');
        $mockedRedisConnection->shouldNotReceive('sAdd');

        $service = new Inge7Service(
            connection: $mockedRedisConnection,
            cryptoService: $crypto,
            identityHashService: $identityHashService,
            providerIdentifier: 'XXX',
        );

        // Send test to redis
        $service->setTest($test);

        // Assert redis keys
        $this->assertSame("$identityHash:holder", $redisHolderKey);

        // Assert holder information
        $holderInformation = json_decode($crypto->decrypt(base64_decode($redisEncryptedHolderInformation)), true);
        $this->assertSame([
            "protocolVersion" => "3.0",
            "providerIdentifier" => "XXX",
            "status" => "complete",
            "holder" => [
                "bsn" => '123456789',
                "firstName" => 'Bob',
                "infix" => 'de',
                "lastName" => 'Bouwer',
                "birthDate" => '1990-01-01',
            ]
        ], $holderInformation);
    }

    /**
     * @throws SodiumException
     */
    public function testDigestInformationToRedis(): void
    {
        // Create test based on fixed data
        $test = Test::factory()
            ->withEuValues('A')
            ->create([
                'bsn' => '123456789',
                'initials' => 'B',
                'insertion' => 'de',
                'surname' => 'Bouwer',
                'birthdate' => '1990-01-01',
                'brp_first_names' => 'Bob',
                'brp_prefix_surname' => 'de',
                'brp_surname' => 'Bouwer',
                'brp_date_of_birth' => '19900101',
            ]);

        // Create identity hash based on fixed data
        $identityHashService = new IdentityHashService('some-secret');
        $identityHash = $identityHashService->getIdentityHash(
            bsn: '123456789',
            firstName: 'Bob',
            lastName: 'Bouwer',
            birthDate: '19900101',
        );

        // Initialise mocks
        $crypto = $this->getSealboxCryptoService();

        $mockedRedisConnection = Mockery::mock(PredisConnection::class);
        $mockedRedisConnection
            ->shouldReceive('set')
            ->once()
            ->withArgs([
                Mockery::capture($redisHolderKey),
                Mockery::capture($redisEncryptedHolderInformation),
                'EX',
                86400,
            ]);

        $mockedRedisConnection
            ->shouldReceive('transaction')
            ->once()
            ->withArgs(function (Closure $closure) {
                $closure();
                return true;
            });
        $mockedRedisConnection
            ->shouldReceive('sismember')
            ->once()
            ->withArgs([$identityHash . ':digests', hash('sha256', $test->id)])
            ->andReturn(false);
        $mockedRedisConnection
            ->shouldReceive('sAdd')
            ->once()
            ->withArgs([$identityHash . ':events', Mockery::capture($redisEncryptedEventInformation)]);
        $mockedRedisConnection
            ->shouldReceive('sAdd')
            ->once()
            ->withArgs([$identityHash . ':digests', Mockery::capture($redisDigestsInformation)]);
        $mockedRedisConnection
            ->shouldReceive('expire')
            ->twice();

        $service = new Inge7Service(
            connection: $mockedRedisConnection,
            cryptoService: $crypto,
            identityHashService: $identityHashService,
            providerIdentifier: 'XXX',
        );

        // Send test to redis
        $service->setTest($test);

        // Assert redis keys
        $this->assertSame("$identityHash:holder", $redisHolderKey);

        $this->assertNotNull($redisEncryptedHolderInformation, 'Redis did not receive holder information');
        $this->assertNotNull($redisEncryptedEventInformation, 'Redis did not receive event information');
        $this->assertNotNull($redisDigestsInformation, 'Redis did not receive digest information');

        // Assert sadd digest information
        $this->assertSame(hash('sha256', $test->id), $redisDigestsInformation[0]);
    }

    /**
     * @throws SodiumException
     */
    public function testEventInformationToRedis(): void
    {
        // Create test based on fixed data
        $test = Test::factory()
            ->withEuValues('A')
            ->create([
                'bsn' => '123456789',
                'initials' => 'B',
                'insertion' => 'de',
                'surname' => 'Bouwer',
                'birthdate' => '1990-01-01',
                'brp_first_names' => 'Bob',
                'brp_prefix_surname' => 'de',
                'brp_surname' => 'Bouwer',
                'brp_date_of_birth' => '19900101',
                'is_specimen' => true,
            ]);

        // Create identity hash based on fixed data
        $identityHashService = new IdentityHashService('some-secret');
        $identityHash = $identityHashService->getIdentityHash(
            bsn: '123456789',
            firstName: 'Bob',
            lastName: 'Bouwer',
            birthDate: '19900101',
        );

        // Initialise mocks
        $crypto = $this->getSealboxCryptoService();

        $mockedRedisConnection = Mockery::mock(PredisConnection::class);
        $mockedRedisConnection
            ->shouldReceive('set')
            ->once()
            ->withArgs([
                Mockery::capture($redisHolderKey),
                Mockery::capture($redisEncryptedHolderInformation),
                'EX',
                86400
            ]);

        $mockedRedisConnection
            ->shouldReceive('transaction')
            ->once()
            ->withArgs(function (Closure $closure) {
                $closure();
                return true;
            });
        $mockedRedisConnection
            ->shouldReceive('sismember')
            ->once()
            ->withArgs([$identityHash . ':digests', hash('sha256', $test->id)])
            ->andReturn(false);
        $mockedRedisConnection
            ->shouldReceive('sAdd')
            ->once()
            ->withArgs([$identityHash . ':events', Mockery::capture($redisEncryptedEventInformation)]);
        $mockedRedisConnection
            ->shouldReceive('sAdd')
            ->once()
            ->withArgs([$identityHash . ':digests', Mockery::capture($redisDigestsInformation)]);
        $mockedRedisConnection
            ->shouldReceive('expire')
            ->twice();

        $service = new Inge7Service(
            connection: $mockedRedisConnection,
            cryptoService: $crypto,
            identityHashService: $identityHashService,
            providerIdentifier: 'XXX',
        );

        // Send test to redis
        $service->setTest($test);

        $this->assertNotNull($redisEncryptedHolderInformation, 'Redis did not receive holder information');
        $this->assertNotNull($redisEncryptedEventInformation, 'Redis did not receive event information');
        $this->assertNotNull($redisDigestsInformation, 'Redis did not receive digest information');

        // Assert event information
        $eventInformation = json_decode($crypto->decrypt(base64_decode($redisEncryptedEventInformation[0])), true);
        $this->assertSame([
            'type' => 'positivetest',
            'unique' => $test->id,
            'isSpecimen' => true,
            'positivetest' => [
                'sampleDate' => $test->date_of_sample_collection->toIso8601ZuluString(),
                'positiveResult' => true,
                'facility' => $test->involved_company,
                'type' => 'LP217198-3',
                'name' => 'Panbio COVID-19 Ag Test',
                'manufacturer' => '1232',
            ]
        ], $eventInformation);
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
}
