<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class UziDevelopMiddleware
 *
 * This class is used in local development only to mimic an existing UZI pass. This is useful for testing incorrect
 * or invalid UZI passes, or just test the UZI pass authentication system without having a pass and/or card reader
 * present.
 *
 * @package App\Http\Middleware
 */
class UziDevelopMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('app.debug') && config('uzi.override_development_cert')) {
            $cert = @file_get_contents(base_path(config('uzi.override_development_cert')));
            if ($cert === false) {
                throw new HttpException(512, "UZI certificate provided by OVERRIDE_UZI_CERT was not found");
            }

            $request->server->set('SSL_CLIENT_CERT', $cert);
            $request->server->set('SSL_CLIENT_VERIFY', 'SUCCESS');
        }

        return $next($request);
    }
}
