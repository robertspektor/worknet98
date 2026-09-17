<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\CitynetController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\OfficeController;
use App\Http\Middleware\EnsurePlayerIsEmployed;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComputerController::class, 'show'])->name('home');

Route::get('office', [OfficeController::class, 'show'])
    ->middleware(EnsurePlayerIsEmployed::class)
    ->name('office');

Route::get('citynet', [CitynetController::class, 'show'])
    ->middleware(['auth', 'can:enter-citynet'])
    ->name('citynet');

Route::put('locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware('guest')->group(function (): void {
    Route::post('sign-in', [SignInController::class, 'store'])
        ->middleware('throttle:sign-in')
        ->name('sign-in.store');

    Route::get('login/{token}', [LoginController::class, 'show'])->name('login.show');
    Route::post('login/{token}', [LoginController::class, 'store'])
        ->middleware('throttle:login-redemptions')
        ->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
