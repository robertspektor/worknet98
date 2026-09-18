<?php

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseKind;
use App\Models\Email;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->employment = employAtSeededPosition('flowright-plumbing');
    $this->player = User::findOrFail($this->employment->user_id);
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => 'priya-raman'])->assertSuccessful();
    $this->workCase = WorkCase::query()->where('kind', WorkCaseKind::Template)->sole();
});

it('lets a colleague take over quietly while the player is away', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    $this->travelTo('2026-09-23 09:00:00');
    $this->artisan('game:tick')->assertSuccessful();

    $workCase = $this->workCase->fresh();
    expect($workCase?->employment_id)->toBeNull()
        ->and($workCase?->taken_over_from_employment_id)->toBe($this->employment->id)
        ->and($workCase?->taken_over_at)->not->toBeNull()
        ->and(app(MetricBook::class)->valueOf($this->employment, Metric::Reliability))->toBe(0)
        ->and(Email::query()->whereBelongsTo($this->player)->where('subject', 'Priya Raman: handed over')->exists())->toBeFalse();
});

it('still takes the case away with a word from the superior while the player is around', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    $this->travelTo('2026-09-23 08:00:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    $this->travelTo('2026-09-23 09:00:00');
    $this->artisan('game:tick')->assertSuccessful();

    expect($this->workCase->fresh()?->employment_id)->toBeNull()
        ->and(app(MetricBook::class)->valueOf($this->employment, Metric::Reliability))->toBeLessThan(0)
        ->and(Email::query()->whereBelongsTo($this->player)->where('subject', 'Priya Raman: handed over')->exists())->toBeTrue();
});

it('greets the player coming back with what happened while they were away', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    $this->travelTo('2026-09-24 09:00:00');
    $this->artisan('game:tick')->assertSuccessful();
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    $digest = Email::query()->whereBelongsTo($this->player)->where('subject', 'Back at the desk')->sole();
    expect($digest->sender_name)->toBe('Gary Flowright')
        ->and($digest->body)->toContain('You were gone for 3 days')
        ->toContain('A colleague took one of your cases')
        ->toContain('Your balance: 0 C.');
});

it('sends no digest when the player comes back the same day', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');
    workAs($this->player, 'POST', 'api.v1.shift.clock-out');

    $this->travelTo('2026-09-21 17:00:00');
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    expect(Email::query()->whereBelongsTo($this->player)->where('subject', 'Back at the desk')->exists())->toBeFalse();
});

it('sends no digest on the first shift of a new job', function () {
    workAs($this->player, 'POST', 'api.v1.shift.clock-in');

    expect(Email::query()->whereBelongsTo($this->player)->where('subject', 'Back at the desk')->exists())->toBeFalse();
});
