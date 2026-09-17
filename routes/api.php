<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\CalendarEntryController;
use App\Http\Controllers\Api\V1\ChatMessageController;
use App\Http\Controllers\Api\V1\ChatReadController;
use App\Http\Controllers\Api\V1\ChatReplyController;
use App\Http\Controllers\Api\V1\CompanySoftwareController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DeskPartInstallationController;
use App\Http\Controllers\Api\V1\DeskPlacementController;
use App\Http\Controllers\Api\V1\DiskFileController;
use App\Http\Controllers\Api\V1\EmailController;
use App\Http\Controllers\Api\V1\EmailReadController;
use App\Http\Controllers\Api\V1\FloppyDiskController;
use App\Http\Controllers\Api\V1\FloppyDiskLabelController;
use App\Http\Controllers\Api\V1\HardwareShopController;
use App\Http\Controllers\Api\V1\HomeComputerController;
use App\Http\Controllers\Api\V1\InstalledProgramController;
use App\Http\Controllers\Api\V1\JobApplicationController;
use App\Http\Controllers\Api\V1\JobOpeningController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\ParcelController;
use App\Http\Controllers\Api\V1\PlayerController;
use App\Http\Controllers\Api\V1\PromotionOfferAcceptanceController;
use App\Http\Controllers\Api\V1\PromotionOfferDeclineController;
use App\Http\Controllers\Api\V1\ScheduleController;
use App\Http\Controllers\Api\V1\ShiftController;
use App\Http\Controllers\Api\V1\ShopController;
use App\Http\Controllers\Api\V1\ThermalPasteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->name('api.v1.')->group(function (): void {
    Route::get('player', [PlayerController::class, 'show'])->name('player.show');
    Route::get('home-computer', [HomeComputerController::class, 'show'])->name('home-computer.show');
    Route::post('home-computer/desk-parts/{deskPart}/installation', [DeskPartInstallationController::class, 'store'])
        ->middleware('can:install,deskPart')
        ->name('home-computer.desk-parts.installation.store');
    Route::post('home-computer/thermal-paste', [ThermalPasteController::class, 'store'])->name('home-computer.thermal-paste.store');

    Route::get('desk-placements', [DeskPlacementController::class, 'index'])->name('desk-placements.index');
    Route::post('desk-placements', [DeskPlacementController::class, 'store'])->name('desk-placements.store');

    Route::get('job-openings', [JobOpeningController::class, 'index'])->name('job-openings.index');
    Route::get('job-applications', [JobApplicationController::class, 'index'])->name('job-applications.index');
    Route::post('job-openings/{jobOpening}/applications', [JobApplicationController::class, 'store'])->name('job-openings.applications.store');

    Route::get('emails', [EmailController::class, 'index'])->name('emails.index');
    Route::post('emails', [EmailController::class, 'store'])->name('emails.store');
    Route::post('emails/{email}/read', [EmailReadController::class, 'store'])
        ->middleware('can:update,email')
        ->name('emails.read.store');

    Route::post('promotion-offers/{promotionOffer}/acceptance', [PromotionOfferAcceptanceController::class, 'store'])
        ->middleware('can:respond,promotionOffer')
        ->name('promotion-offers.acceptance.store');
    Route::post('promotion-offers/{promotionOffer}/decline', [PromotionOfferDeclineController::class, 'store'])
        ->middleware('can:respond,promotionOffer')
        ->name('promotion-offers.decline.store');

    Route::get('chat-messages', [ChatMessageController::class, 'index'])->name('chat-messages.index');
    Route::post('chat-messages/read', [ChatReadController::class, 'store'])->name('chat-messages.read.store');
    Route::post('chat-messages/{chatMessage}/replies', [ChatReplyController::class, 'store'])
        ->middleware('can:reply,chatMessage')
        ->name('chat-messages.replies.store');

    Route::get('shift', [ShiftController::class, 'show'])->name('shift.show');
    Route::post('shift/clock-in', [ShiftController::class, 'clockIn'])->name('shift.clock-in');
    Route::post('shift/heartbeat', [ShiftController::class, 'heartbeat'])->name('shift.heartbeat');
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
    Route::put('floppy-disks/{disk}/label', [FloppyDiskLabelController::class, 'update'])
        ->middleware('can:label,disk')
        ->name('floppy-disks.label.update');
    Route::get('floppy-disks/{disk}/files', [DiskFileController::class, 'index'])
        ->middleware('can:view,disk')
        ->name('floppy-disks.files.index');
    Route::post('floppy-disks/{disk}/files', [DiskFileController::class, 'store'])
        ->middleware('can:write,disk')
        ->name('floppy-disks.files.store');
    Route::delete('disk-files/{diskFile}', [DiskFileController::class, 'destroy'])
        ->middleware('can:delete,diskFile')
        ->name('disk-files.destroy');
    Route::get('installed-programs', [InstalledProgramController::class, 'index'])->name('installed-programs.index');
    Route::post('disk-files/{diskFile}/installation', [InstalledProgramController::class, 'store'])
        ->middleware('can:install,diskFile')
        ->name('disk-files.installation.store');

    Route::get('shop/floppy-disks', [ShopController::class, 'index'])->name('shop.floppy-disks.index');
    Route::post('shop/floppy-disks/{floppyDisk}/orders', [ShopController::class, 'store'])->name('shop.floppy-disks.orders.store');
    Route::get('parcels', [ParcelController::class, 'index'])->name('parcels.index');
    Route::get('shop/hardware-parts', [HardwareShopController::class, 'index'])->name('shop.hardware-parts.index');
    Route::post('shop/hardware-parts/{hardwarePart}/orders', [HardwareShopController::class, 'store'])->name('shop.hardware-parts.orders.store');
    Route::post('parcels/{order}/unpacking', [ParcelController::class, 'store'])
        ->middleware('can:unpack,order')
        ->name('parcels.unpacking.store');

    Route::get('note', [NoteController::class, 'show'])->name('note.show');
    Route::put('note', [NoteController::class, 'update'])->name('note.update');
});
