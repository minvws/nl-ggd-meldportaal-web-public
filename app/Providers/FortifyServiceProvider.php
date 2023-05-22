<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Http\Responses\FailedTwoFactorLoginResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse as FailedTwoFactorLoginResponseContract;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(
            TwoFactorAuthenticationProviderContract::class,
            TwoFactorAuthenticationProvider::class
        );

        $this->app->singleton(
            FailedTwoFactorLoginResponseContract::class,
            FailedTwoFactorLoginResponse::class
        );

        Fortify::loginView(function () {
            return view('auth.login');
        });

        // Login with lowercase email
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', strtolower($request->email))->first();
            if (
                $user &&
                Hash::check($request->password ?? '', $user->password ?? '')
            ) {
                return $user;
            }
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });
    }
}
