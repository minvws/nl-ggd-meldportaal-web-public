<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\FormController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*/ Unauthenticated User Routes /*/
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/logout', function () {
    return view('auth.logout');
})->name('auth.logout');

// Change the language of the page
Route::get('ChangeLanguage/{locale}', function ($locale) {
    if (in_array($locale, Config::get('app.locales'))) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('changelang');


Route::middleware(['isAuthenticatedUser', 'active'])->group(function () {

    Route::middleware(['password.confirmed'])->group(function () {
        // Main page
        Route::get('/', [FormController::class, 'index'])->name('home');
        Route::post('/', [FormController::class, 'encryptedStore'])->name('store.encrypted');

        Route::post('/address', [FormController::class, 'address'])
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->name('address')
        ;
    });

    // View/update password
    Route::get('/account', function () {
        if (Auth::user() && Auth::user()->canChangePassword()) {
            return view('profile.show');
        }
        return redirect("home");
    })->name('profile.show');

//    // Update password
    Route::post('/account/update-password', [AccountController::class, 'changePassword'])
        ->name('profile.update_password');
});
