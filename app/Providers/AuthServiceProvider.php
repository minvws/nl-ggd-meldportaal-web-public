<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\UziServerCertificateGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use MinVWS\PUZI\Laravel\CaParser;
use MinVWS\PUZI\UziConstants;
use MinVWS\PUZI\UziReader;
use MinVWS\PUZI\UziValidator;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var  array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->app->bind(UziServerCertificateGuard::class, function () {
            $reader = new UziReader();
            $validator = new UziValidator(
                reader: $reader,
                strictCaCheck: config('uzi.strict_ca_check'),
                allowedTypes: [UziConstants::UZI_TYPE_SERVER],
                allowedRoles: [],
                caCerts: CaParser::getCertsFromFile(config("uzi.ca_certs_path")),
            );

            return new UziServerCertificateGuard($reader, $validator);
        });

        Auth::viaRequest('uzi-server-certificate', $this->app->make(UziServerCertificateGuard::class));
    }
}
