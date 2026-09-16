<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LoginLinkController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\OfficeController;
use App\Http\Middleware\EnsurePlayerIsEmployed;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComputerController::class, 'show'])->name('home');

Route::get('office', [OfficeController::class, 'show'])
    ->middleware(EnsurePlayerIsEmployed::class)
    ->name('office');

Route::put('locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware('guest')->group(function (): void {
    Route::post('login-link', [LoginLinkController::class, 'store'])
        ->middleware('throttle:login-links')
        ->name('login-link.store');

    Route::get('login/{token}', [LoginController::class, 'show'])->name('login.show');
    Route::post('login/{token}', [LoginController::class, 'store'])
        ->middleware('throttle:login-redemptions')
        ->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
