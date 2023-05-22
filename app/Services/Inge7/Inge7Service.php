<?php

declare(strict_types=1);

namespace App\Services\Inge7;

use App\Models\Test;
use App\Services\Inge7\Data\EventInformation;
use App\Services\Inge7\Data\HolderInformation;
use App\Services\Inge7\Data\PositiveTestEventInformation;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\Facades\Log;
use MinVWS\Crypto\Laravel\SealboxCryptoInterface;

/**
 * The Inge7 Service is used to add events to the redis instance of Inge 7.
 * See the link for the event data structures.
 *
 * @link https://github.com/minvws/nl-covid19-coronacheck-provider-docs/blob/main/docs/data-structures-overview.md
 */
class Inge7Service
{
    public function __construct(
        protected PredisConnection $connection,
        protected SealboxCryptoInterface $cryptoService,
        protected IdentityHashService $identityHashService,
        protected string $providerIdentifier,
        protected int $ttl = 24 * 60 * 60,
    ) {
    }

    public function unsetTest(Test $test): void
    {
        if (!$this->testCanBeSynced($test)) {
            return;
        }

        $identityHash = $this->getIdentityHash($test);

        $keys = [
            $identityHash . ':holder',
            $identityHash . ':events',
            $identityHash . ':digests',
        ];

        $this->connection->del($keys);
    }

    public function setTest(Test $test): void
    {
        if (!$this->testCanBeSynced($test)) {
            Log::warning('Test cannot be synced, no bsn or eu_event_type available.', ['id' => $test->id]);
            return;
        }

        $identityHash = $this->getIdentityHash($test);

        $this->setHolderInformation($identityHash, new HolderInformation(
            providerIdentifier:  $this->providerIdentifier,
            bsn: $test->bsn,
            firstName: $test->brp_first_names ?? '',
            infix: $test->brp_prefix_surname,
            lastName: $test->brp_surname ?? '',
            birthDate: $test->brp_date_of_birth ?? '',
        ));
        $this->addEventInformation($identityHash, new PositiveTestEventInformation(
            uniqueIdentifier: $test->id,
            isSpecimen: $test->is_specimen ?? false,
            sampleDate: $test->date_of_sample_collection->toIso8601ZuluString(),
            positiveResult: true,
            facility: $test->involved_company ?? '',
            type: $test->eu_event_type ?? '',
            name: $test->eu_event_name ?? '',
            manufacturer: $test->eu_event_manufacturer,
        ));
    }

    protected function setHolderInformation(string $identityHash, HolderInformation $holderInformation): void
    {
        $encrypted = $this->cryptoService->encrypt($holderInformation->__toString());

        $this->connection->set($identityHash . ':holder', base64_encode($encrypted), 'EX', $this->ttl);
    }

    protected function addEventInformation(string $identityHash, EventInformation $test): void
    {
        $encrypted = $this->cryptoService->encrypt($test->__toString());

        $hashedIdentifier = hash('sha256', $test->getUniqueIdentifier());

        $this->connection->transaction(function () use ($identityHash, $encrypted, $hashedIdentifier): void {
            if ($this->eventExists($identityHash, $hashedIdentifier)) {
                return;
            }

            $this->connection->sadd($identityHash . ':events', [base64_encode($encrypted)]);
            $this->connection->sadd($identityHash . ':digests', [$hashedIdentifier]);

            $this->connection->expire($identityHash . ':events', $this->ttl);
            $this->connection->expire($identityHash . ':digests', $this->ttl);
        });
    }

    protected function eventExists(string $identityHash, string $hashedIdentifier): bool
    {
        return (bool) $this->connection->sismember($identityHash . ':digests', $hashedIdentifier) === true;
    }

    protected function testCanBeSynced(Test $test): bool
    {
        return $test->eu_event_type !== null
            && !empty($test->bsn);
    }

    protected function getIdentityHash(Test $test): string
    {
        return $this->identityHashService->getIdentityHash(
            bsn: $test->bsn,
            firstName: $test->brp_first_names ?? '',
            lastName: $test->brp_surname ?? '',
            birthDate: $test->brp_date_of_birth ?? '',
        );
    }
}
