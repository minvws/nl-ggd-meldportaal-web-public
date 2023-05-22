<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class Role
 *
 * Middleware that will check against specific roles
 *
 * @package App\Http\Middleware
 * @author: jthijssen@noxlogic.nl
 */
class Role
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->hasRole($roles)) {
                return $next($request);
            }
        }

        $user = Auth::user();
        Log::alert('User without correct role visiting route', [
            'user_id' => $user->id ?? null,
            'ip_address' => request()->ip(),
            'uri' => $request->getUri(),
        ]);

        return redirect('/');
    }
}
