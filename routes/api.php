<?php

declare(strict_types=1);

use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::post('tests', \App\Http\Controllers\Api\V1\TestController::class)
        ->middleware('auth:uzi-server-certificate')
        ->name('tests');
});

Route::prefix('v2')->name('v2.')->group(function () {
    Route::post('tests', \App\Http\Controllers\Api\V2\TestController::class)
        ->middleware('auth:uzi-server-certificate')
        ->name('tests');
});

Route::get('health', HealthController::class)
    ->middleware('auth:uzi-server-certificate')
    ->name('health');
