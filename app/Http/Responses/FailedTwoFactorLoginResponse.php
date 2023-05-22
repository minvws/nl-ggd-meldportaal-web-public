<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\User;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use MinVWS\Logging\Laravel\LogService;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginTwoFactorFailedEvent;
use Symfony\Component\HttpFoundation\Response;

class FailedTwoFactorLoginResponse extends \Laravel\Fortify\Http\Responses\FailedTwoFactorLoginResponse
{
    public function __construct(private LogService $logService)
    {
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param TwoFactorLoginRequest $request
     * @return Response
     */
    public function toResponse($request): Response
    {
        if (!$request->hasChallengedUser()) {
            return parent::toResponse($request);
        }

        /** @var User $user */
        $user = $request->challengedUser();

        $this->logService->log((new UserLoginTwoFactorFailedEvent())
            ->asExecute()
            ->withActor($user)
            ->withSource(config('app.name'))
            ->withData([
                'last_active_at' => $user->last_active_at,
                'last_login_at' => $user->last_login_at,
            ]));

        return parent::toResponse($request);
    }
}
