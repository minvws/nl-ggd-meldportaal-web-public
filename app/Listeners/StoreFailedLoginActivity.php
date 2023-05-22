<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Failed;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\LogService;

/**
 * Listener that will log any failed login activity
 *
 * @package App\Listeners
 * @author jthijssen@noxlogic.nl
 */
class StoreFailedLoginActivity
{
    public function __construct(
        private LogService $logService
    ) {
    }

    /**
     * Handle the event.
     *
     * @param Failed $event
     * @return void
     */
    public function handle(Failed $event): void
    {
        // Possible user that is trying to login
        $user = $this->getLoginUser($event);

        $this->logService->log((new UserLoginLogEvent())
            ->asExecute()
            ->withSource(config('app.name'))
            ->withFailed(true, $user?->id ? 'invalid_password' : 'invalid_email')
            ->withData([
                'user_id' => $user?->id,
                'last_active_at' => $user?->last_active_at,
                'last_login_at' => $user?->last_login_at,
                'user_roles' => $user?->roles,
                'user_email' => $user?->email ?? $event->credentials['email'],
                'partial_password_hash' => substr(hash("sha256", $event->credentials['password']), 0, 16),
            ]));
    }

    private function getLoginUser(Failed $event): ?User
    {
        /** @var User|null $user */
        $user = $event->user;
        if (!is_null($user)) {
            return $user;
        }

        $user = User::whereEmail($event->credentials['email'])->first();
        if (!is_null($user)) {
            return $user;
        }

        return null;
    }
}
