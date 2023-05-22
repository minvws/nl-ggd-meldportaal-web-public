<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Logout;
use MinVWS\Logging\Laravel\Events\Logging\UserLogoutLogEvent;
use MinVWS\Logging\Laravel\LogService;

/**
 * Listener that will log the logout event
 *
 * @package App\Listeners
 * @author rick@rl-webdiensten.nl
 */
class LogLogoutEvent
{
    public function __construct(
        private LogService $logService
    ) {
    }

    /**
     * Handle the event.
     *
     * @param Logout $event
     * @return void
     */
    public function handle(Logout $event): void
    {
        /** @var int $userId */
        $userId = $event->user->getAuthIdentifier();

        /** @var User $user */
        $user = User::find($userId);

        $this->logService->log((new UserLogoutLogEvent())
            ->asExecute()
            ->withActor($user)
            ->withSource(config('app.name'))
            ->withData([
                'last_active_at' => $user->last_active_at,
                'last_login_at' => $user->last_login_at,
            ]));
    }
}
