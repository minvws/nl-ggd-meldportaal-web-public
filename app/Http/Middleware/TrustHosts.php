<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array
     */
    public function hosts()
    {
        $additionalTrustedHosts = config('app.trusted_hosts', []);

        return [
            $this->allSubdomainsOfApplicationUrl(),
            ...$additionalTrustedHosts,
        ];
    }
}
