<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\LogService;

/**
 * Listener that will log any login activity
 *
 * @package App\Listeners
 * @author jthijssen@noxlogic.nl
 */
class StoreLoginActivity
{
    public const SCOPE = "meldportaal";

    public function __construct(
        private LogService $logService
    ) {
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        $this->logService->log((new UserLoginLogEvent())
            ->asExecute()
            ->withActor($user)
            ->withData([
                'user_id' => $user->id,
                'last_active_at' => $user->last_active_at,
                'last_login_at' => $user->last_login_at,
                'user_email' => $user->email,
                'user_roles' => $user->roles,
            ]));

        LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => app('request')->ip(),
            'logged_in_at' => Carbon::now(),
            'scope' => self::SCOPE,
        ]);
    }
}
