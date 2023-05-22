<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\User;
use App\Role;
use Illuminate\Http\Request;
use MinVWS\PUZI\UziReader;
use MinVWS\PUZI\UziValidator;

class UziServerCertificateGuard
{
    public function __construct(
        protected UziReader $reader,
        protected UziValidator $validator
    ) {
    }

    public function __invoke(Request $request): ?User
    {
        if (!$this->validator->isValid($request)) {
            return null;
        }

        $uziInfo = $this->reader->getDataFromRequest($request);
        $uziSerial = $uziInfo?->getSerialNumber();
        if (empty($uziSerial)) {
            return null;
        }

        $user = User::where('uzi_serial', $uziSerial)->first();
        if ($user === null) {
            return null;
        }

        if ($user->hasRole(Role::API) === false) {
            return null;
        }

        return $user;
    }
}
