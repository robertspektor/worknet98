<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\CalendarEntryController;
use App\Http\Controllers\Api\V1\CompanySoftwareController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\EmailController;
use App\Http\Controllers\Api\V1\EmailReadController;
use App\Http\Controllers\Api\V1\FloppyDiskController;
use App\Http\Controllers\Api\V1\InstalledProgramController;
use App\Http\Controllers\Api\V1\JobApplicationController;
use App\Http\Controllers\Api\V1\JobOpeningController;
use App\Http\Controllers\Api\V1\PlayerController;
use App\Http\Controllers\Api\V1\ScheduleController;
use App\Http\Controllers\Api\V1\ShiftController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->name('api.v1.')->group(function (): void {
    Route::get('player', [PlayerController::class, 'show'])->name('player.show');

    Route::get('job-openings', [JobOpeningController::class, 'index'])->name('job-openings.index');
    Route::get('job-applications', [JobApplicationController::class, 'index'])->name('job-applications.index');
    Route::post('job-openings/{jobOpening}/applications', [JobApplicationController::class, 'store'])->name('job-openings.applications.store');

    Route::get('emails', [EmailController::class, 'index'])->name('emails.index');
    Route::post('emails', [EmailController::class, 'store'])->name('emails.store');
    Route::post('emails/{email}/read', [EmailReadController::class, 'store'])
        ->middleware('can:update,email')
        ->name('emails.read.store');

    Route::get('shift', [ShiftController::class, 'show'])->name('shift.show');
    Route::post('shift/clock-in', [ShiftController::class, 'clockIn'])->name('shift.clock-in');
    Route::post('shift/clock-out', [ShiftController::class, 'clockOut'])->name('shift.clock-out');

    Route::get('company-software', [CompanySoftwareController::class, 'show'])->name('company-software.show');
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('schedule', [ScheduleController::class, 'show'])->name('schedule.show');
    Route::post('appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('can:delete,appointment')
        ->name('appointments.destroy');

    Route::get('calendar-entries', [CalendarEntryController::class, 'index'])->name('calendar-entries.index');
    Route::post('calendar-entries', [CalendarEntryController::class, 'store'])->name('calendar-entries.store');
    Route::delete('calendar-entries/{calendarEntry}', [CalendarEntryController::class, 'destroy'])
        ->middleware('can:delete,calendarEntry')
        ->name('calendar-entries.destroy');
    Route::get('floppy-disks', [FloppyDiskController::class, 'index'])->name('floppy-disks.index');
    Route::get('installed-programs', [InstalledProgramController::class, 'index'])->name('installed-programs.index');
    Route::post('floppy-disks/{floppyDisk}/installation', [InstalledProgramController::class, 'store'])
        ->middleware('can:install,floppyDisk')
        ->name('floppy-disks.installation.store');
});
