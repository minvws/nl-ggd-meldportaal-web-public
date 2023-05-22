<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/**
 * Class ForceChangePassword
 *
 * Redirects a user to their profile page when their password has not been changed for the first time yet.
 *
 * @package App\Http\Middleware
 */
class ForceChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                /** @var User $user */
                $user = Auth::user();

                // Uzi pass users do not need to update a password, they can always continue
                if ($user->isUzi() === true) {
                    return $next($request);
                }

                // redirect to profile/password change page when user hasn't updated the password yet.
                if ($user->password_updated_at == null) {
                    return Redirect::route('profile.show');
                }
            }
        }

        return $next($request);
    }
}
