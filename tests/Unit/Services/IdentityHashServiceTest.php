<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\Inge7\IdentityHashService;
use Tests\Unit\TestCase;

class IdentityHashServiceTest extends TestCase
{
    public function testHash(): void
    {
        $service = new IdentityHashService('a-special-secret');
        $hash = $service->getIdentityHash(
            bsn: '123456789',
            firstName: 'John',
            lastName: 'Doe',
            birthDate: '19900101'
        );
        $this->assertEquals('f1fac84b0afe78cebffa65ec8e5e5f6223305f4bd35a061093a24abde9f7f20b', $hash);

        $service = new IdentityHashService('another-special-secret');
        $hash = $service->getIdentityHash(
            bsn: '123456789',
            firstName: 'John',
            lastName: 'Doe',
            birthDate: '19900101'
        );
        $this->assertNotEquals('f1fac84b0afe78cebffa65ec8e5e5f6223305f4bd35a061093a24abde9f7f20b', $hash);
        $this->assertEquals('70bb2a19812ba53c1dee6f54ceb93ea3daeb622a3d6829f4e02728f62a22fbbb', $hash);
    }
}
