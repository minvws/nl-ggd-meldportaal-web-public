<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Middleware that will check for the logo key in the request (query string) and saves it into the session. This will
 * be used to display the correct logo on the page (which can be toggled on staging environments)
 *
 * @package App\Http\Middleware
 */
class Logo
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set the logo request into the session
        if ($request->has('logo')) {
            $request->session()->put('logo', $request->get('logo'));
        }

        return $next($request);
    }
}
