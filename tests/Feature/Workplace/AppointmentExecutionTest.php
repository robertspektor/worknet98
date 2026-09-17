<?php

use App\Models\Appointment;
use App\Workplace\AppointmentExecutor;

beforeEach(fn () => $this->travelTo('2026-09-22 09:00:00'));

it('carries out an appointment two game hours after its slot starts', function () {
    $appointment = Appointment::factory()->create(['date' => '2026-09-22', 'slot' => '10:00']);

    $this->travelTo('2026-09-22 11:59:59');
    app(AppointmentExecutor::class)->executeDue();
    expect($appointment->fresh()?->executed_at)->toBeNull();

    $this->travelTo('2026-09-22 12:00:00');
    expect(app(AppointmentExecutor::class)->executeDue())->toBe(1)
        ->and($appointment->fresh()?->executed_at)->not->toBeNull();
});

it('carries out appointments of earlier days and never twice', function () {
    Appointment::factory()->create(['date' => '2026-09-21', 'slot' => '15:00']);

    expect(app(AppointmentExecutor::class)->executeDue())->toBe(1)
        ->and(app(AppointmentExecutor::class)->executeDue())->toBe(0);
});

it('follows the game clock instead of real time', function () {
    config(['game.clock' => ['scale' => 7, 'real_epoch' => '2026-09-22 00:00:00', 'game_epoch' => '2026-09-22 00:00:00']]);
    $appointment = Appointment::factory()->create(['date' => '2026-09-23', 'slot' => '08:00']);

    $this->travelTo('2026-09-22 05:00:00');
    app(AppointmentExecutor::class)->executeDue();

    expect($appointment->fresh()?->executed_at)->not->toBeNull();
});
