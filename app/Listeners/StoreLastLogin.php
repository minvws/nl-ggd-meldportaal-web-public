<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class StoreLastLogin
 *
 * Listener that will update the last login date of a user
 *
 * @package App\Listeners
 * @author jthijssen@noxlogic.nl
 */
class StoreLastLogin
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = Auth::user();
        if (is_null($user)) {
            return;
        }

        $user->last_login_at = Carbon::Now();
        $user->save();
    }
}
