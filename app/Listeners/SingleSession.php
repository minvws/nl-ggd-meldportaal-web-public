<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class SingleSession
 *
 * Listener that will only allow single sessions for users
 *
 * @package App\Listeners
 * @author jthijssen@noxlogic.nl
 */
class SingleSession
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $user = Auth::user();
        if (is_null($user)) {
            return;
        }

        // Remove all other sessions for this user
        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }
}
