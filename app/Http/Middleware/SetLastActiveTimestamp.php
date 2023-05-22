<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Support\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class SetLastActiveTimestamp
 *
 * Middleware that will update last-active timestamp for a logged-in user
 *
 * @package App\Http\Middleware
 * @author jthijssen@noxlogic.nl
 */
class SetLastActiveTimestamp
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            $user->last_active_at = Carbon::Now();
            $user->save();
        }

        return $next($request);
    }
}
