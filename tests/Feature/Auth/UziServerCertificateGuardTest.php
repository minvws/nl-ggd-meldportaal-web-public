<?php

declare(strict_types=1);

namespace Auth;

use App\Auth\UziServerCertificateGuard;
use App\Role;
use Illuminate\Http\Request;
use MinVWS\PUZI\UziConstants;
use MinVWS\PUZI\UziReader;
use MinVWS\PUZI\UziUser;
use MinVWS\PUZI\UziValidator;
use Tests\Feature\TestCase;
use Tests\Helper\TestUziCertificateGenerator;

class UziServerCertificateGuardTest extends TestCase
{
    public function testGuardReturnsUser(): void
    {
        $serialNumber = '123456789';

        // Create user with API role
        $user = $this->setupUser([
            'uzi_serial' => $serialNumber,
            'roles' => [Role::API]
        ]);

        // Build request with UZI server certificate
        $request = $this->createRequestWithUziServerCertificate($serialNumber);

        // Execute guard with request
        $guard = $this->createUziCertificateGuard();
        $guardUser = $guard->__invoke($request);

        // Assert that guard returns user
        $this->assertNotNull($guardUser, 'Guard did not return a user');
        $this->assertSame($user->id, $guardUser->id);
    }

    public function testGuardReturnsNullWhenUserNotExists(): void
    {
        // Create user with API role
        $user = $this->setupUser([
            'uzi_serial' => '123456789',
            'roles' => [Role::API]
        ]);

        // Build request with UZI server certificate with different serial number
        $request = $this->createRequestWithUziServerCertificate('987654321');

        // Execute guard with request
        $guard = $this->createUziCertificateGuard();
        $guardUser = $guard->__invoke($request);

        // Assert that guard returns user
        $this->assertNull($guardUser);
    }

    public function testGuardReturnsNullWhenUserDoesNotHaveAPIRole(): void
    {
        $serialNumber = '123456789';

        // Create user with API role
        $user = $this->setupUser([
            'uzi_serial' => $serialNumber,
            'roles' => [Role::USER]
        ]);

        // Build request with UZI server certificate with different serial number
        $request = $this->createRequestWithUziServerCertificate($serialNumber);

        // Execute guard with request
        $guard = $this->createUziCertificateGuard();
        $guardUser = $guard->__invoke($request);

        // Assert that guard returns user
        $this->assertNull($guardUser);
    }

    public function testGuardReturnsNullWhenNoCertIsProvided(): void
    {
        // Build request without certificate
        $request = $this->createRequest();

        // Execute guard with request
        $guard = $this->createUziCertificateGuard();
        $guardUser = $guard->__invoke($request);

        // Assert that guard returns user
        $this->assertNull($guardUser);
    }

    public function testGuardReturnsNullWhenCertIsNotAllowed(): void
    {
        // Build request without certificate
        $request = $this->createRequestWithUziServerCertificate('123456789');

        // Execute guard with request
        $guard = $this->createUziCertificateGuard(allowedTypes: []);
        $guardUser = $guard->__invoke($request);

        // Assert that guard returns user
        $this->assertNull($guardUser);
    }

    public function testGuardReturnsNullWhenSerialNumberIsEmpty(): void
    {
        // Mock uzi reader and validator.
        $uziInfo = new UziUser();
        $uziInfo->setSerialNumber('');

        $reader = \Mockery::mock(UziReader::class);
        $reader->expects('getDataFromRequest')
            ->with(\Mockery::type(Request::class))
            ->andReturn($uziInfo);

        $validator = \Mockery::mock(UziValidator::class);
        $validator->expects('isValid')
            ->with(\Mockery::type(Request::class))
            ->andReturn(true);

        // Execute guard with request
        $guard = new UziServerCertificateGuard($reader, $validator);
        $guardUser = $guard->__invoke(new Request());

        // Assert that guard returns null when serial number is empty
        $this->assertNull($guardUser);
    }

    protected function createRequestWithUziServerCertificate(string $serialNumber): Request
    {
        $generator = new TestUziCertificateGenerator();
        [
            'cert' => $certificate,
            'key' => $privateKey,
        ] = $generator->generateCertificate($serialNumber);

        return $this->createRequest($certificate);
    }

    protected function createRequest(?string $certificate = null): Request
    {
        $request = new Request();
        $request->server->replace([
            'SSL_CLIENT_VERIFY' => 'SUCCESS',
            'SSL_CLIENT_CERT' => $certificate,
        ]);
        return $request;
    }

    protected function createUziCertificateGuard(
        array $allowedTypes = [UziConstants::UZI_TYPE_SERVER]
    ): UziServerCertificateGuard {
        $reader = new UziReader();
        $validator = new UziValidator(
            reader: $reader,
            strictCaCheck: false,
            allowedTypes: $allowedTypes,
            allowedRoles: [],
            caCerts: [],
        );

        return new UziServerCertificateGuard($reader, $validator);
    }
}
