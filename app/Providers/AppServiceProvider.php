<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        $this->addValidators();
        $this->addBladeRules();

        Paginator::defaultView('pagination::rijkshuisstijl');
        Paginator::defaultSimpleView('pagination::rijkshuisstijl');
    }

    private function addValidators(): void
    {
        Validator::extend('commonlist', 'App\Validators\CommonList@check');
        Validator::extend('not_numeric', 'App\Validators\NotNumeric@check');
        Validator::extend('similarity', 'App\Validators\Similarity@check');
        Validator::extend('valid_year', 'App\Validators\ValidYear@check');
        Validator::extend('caps', 'App\Validators\Caps@check');
        Validator::extend('bsn', 'App\Validators\Bsn@check');
        Validator::extend('bsn_lookup', 'App\Validators\BsnLookup@check');
    }

    private function addBladeRules(): void
    {
        Blade::if('role', function ($roles) {
            /** @var User $user */
            $user = Auth::user();
            if ($user == null) {
                return false;
            }

            return $user->hasRole($roles);
        });

        Blade::if('langIs', function ($lang) {
            return app()->getLocale() === $lang;
        });
    }
}
