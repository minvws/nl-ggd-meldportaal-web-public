<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Active
 *
 * Middleware that will decline non-active users from requesting pages
 *
 * @package App\Http\Middleware
 * @author jthijssen@noxlogic.nl
 */
class Active
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
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->active) {
                return $next($request);
            }
        }

        /** @var User|null $user */
        $user = Auth::user();
        Log::alert('Non-active user attempting usage', [
            'user_id' => $user?->id,
            'ip_address' => request()->ip()
        ]);

        abort(Response::HTTP_FAILED_DEPENDENCY, 'Access denied');
    }
}
