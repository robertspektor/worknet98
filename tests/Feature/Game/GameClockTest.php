<?php

use App\Game\GameClock;
use Carbon\CarbonImmutable;

beforeEach(fn () => config(['game.clock' => [
    'scale' => 7,
    'real_epoch' => '2026-09-14 00:00:00',
    'game_epoch' => '1998-01-05 00:00:00',
]]));

it('runs one game week per real day', function () {
    $this->travelTo('2026-09-15 00:00:00');

    expect(app(GameClock::class)->now()->toDateTimeString())->toBe('1998-01-12 00:00:00');
});

it('runs a game day in a seventh of a real day', function () {
    $this->travelTo('2026-09-14 12:00:00');

    expect(app(GameClock::class)->now()->toDateTimeString())->toBe('1998-01-08 12:00:00')
        ->and(app(GameClock::class)->today()->toDateString())->toBe('1998-01-08');
});

it('converts game time back to the real time it happens at', function () {
    $real = app(GameClock::class)->toReal(CarbonImmutable::parse('1998-01-06 08:00:00'));

    expect($real->toDateTimeString())->toBe('2026-09-14 04:34:18')
        ->and(app(GameClock::class)->fromReal($real)->toDateTimeString())->toBe('1998-01-06 08:00:06');
});

it('displays real timestamps as game wall time', function () {
    expect(app(GameClock::class)->display(CarbonImmutable::parse('2026-09-14 04:48:00')))->toBe('1998-01-06T09:36:00');
});
