<?php

use App\Http\Controllers\Api\V1\EmailController;
use App\Http\Controllers\Api\V1\EmailReadController;
use App\Http\Controllers\Api\V1\JobApplicationController;
use App\Http\Controllers\Api\V1\JobOpeningController;
use App\Http\Controllers\Api\V1\PlayerController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->name('api.v1.')->group(function (): void {
    Route::get('player', [PlayerController::class, 'show'])->name('player.show');

    Route::get('job-openings', [JobOpeningController::class, 'index'])->name('job-openings.index');
    Route::get('job-applications', [JobApplicationController::class, 'index'])->name('job-applications.index');
    Route::post('job-openings/{jobOpening}/applications', [JobApplicationController::class, 'store'])->name('job-openings.applications.store');

    Route::get('emails', [EmailController::class, 'index'])->name('emails.index');
    Route::post('emails/{email}/read', [EmailReadController::class, 'store'])
        ->middleware('can:update,email')
        ->name('emails.read.store');
});
