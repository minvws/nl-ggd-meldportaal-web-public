<?php

declare(strict_types=1);

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var  array<int, string>
     */
    protected $middleware = [
        \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \App\Http\Middleware\UziDevelopMiddleware::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var  array<string, array<int, string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Spatie\Csp\AddCspHeaders::class,
            \App\Http\Middleware\Logo::class,
            \App\Http\Middleware\Locale::class,
            \App\Http\Middleware\SetLastActiveTimestamp::class,
        ],
        'api' => [
            'throttle:60',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,

        /*/ Laravel Framework Middleware /*/
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        /*/ Package Middleware /*/
        'cms_sign' => \MinVWS\Crypto\Laravel\Http\Middleware\CmsSignature::class,

        /*/ Application Middleware /*/
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'role' => \App\Http\Middleware\Role::class,
        'password.confirmed' => \App\Http\Middleware\ForceChangePassword::class,
        'isAuthenticatedUser' => \App\Http\Middleware\IsAuthenticatedUser::class,
        'active' => \App\Http\Middleware\Active::class,
    ];

    /**
     * Returns the version of the application by fetching and displaying the version.json file
     *
     * @return string URL
     */
    public static function applicationVersion(): string
    {
        // Silence is ok here
        $versionJson = @file_get_contents(public_path("/version.json"));
        if (!$versionJson) {
            return 'Undefined';
        }

        $versionData = json_decode($versionJson, true);
        if (is_array($versionData) && array_key_exists('version', $versionData)) {
            return $versionData['version'];
        }

        return 'Unknown';
    }
}
