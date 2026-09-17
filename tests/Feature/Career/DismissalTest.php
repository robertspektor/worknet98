<?php

use App\Career\Dismissal;
use App\Careers\ApplicationEligibility;
use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\WorkCaseStatus;
use App\Models\Email;
use App\Models\JobOpening;
use App\Models\PerformanceReview;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkCase;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-07-06 09:00:00');
    $this->seed([CompanySeeder::class, BranchSeeder::class]);
    $this->employment = employAtSeededPosition('flowright-plumbing');
    $this->player = User::findOrFail($this->employment->user_id);
});

function havePoorMonthUntil(string $nextMonthStart): void
{
    app(MetricBook::class)->apply(test()->employment, [Metric::Cost->value => -2]);
    test()->travelTo("{$nextMonthStart} 00:05:00");
    test()->artisan('game:tick')->assertSuccessful();
}

it('counts the official warnings in every poor review', function () {
    havePoorMonthUntil('2026-08-01');
    havePoorMonthUntil('2026-09-01');

    expect(Email::query()->where('subject', 'Your review for August 2026')->sole()->body)->toContain('Official warnings in this job: 2 of 3')
        ->and($this->player->fresh()?->employment?->id)->toBe($this->employment->id);
});

it('dismisses the player with the third official warning', function () {
    havePoorMonthUntil('2026-08-01');
    havePoorMonthUntil('2026-09-01');
    $this->travelTo('2026-09-30 23:00:00');
    $this->artisan('cases:open', ['company' => 'flowright-plumbing', 'branch' => 'maple-falls', 'template' => 'leaking-pipe', '--customer' => 'priya-raman'])->assertSuccessful();
    $workCase = WorkCase::query()->latest('id')->firstOrFail();
    expect($workCase->employment_id)->toBe($this->employment->id);

    havePoorMonthUntil('2026-10-01');

    $position = Position::findOrFail($this->employment->position_id);
    expect($this->employment->fresh()?->ended_at)->not->toBeNull()
        ->and($this->player->fresh()?->employment)->toBeNull()
        ->and($position->isHeldByPlayer())->toBeFalse()
        ->and($workCase->fresh()?->employment_id)->toBeNull()
        ->and($workCase->fresh()?->status)->toBe(WorkCaseStatus::Open);

    $letter = Email::query()->where('subject', 'Your employment at Flowright Plumbing & Heating')->sole();
    expect($letter->employment_id)->toBeNull()
        ->and($letter->sender_name)->toBe('Dolores Flowright');
});

it('no longer reviews an ended employment', function () {
    havePoorMonthUntil('2026-08-01');
    havePoorMonthUntil('2026-09-01');
    havePoorMonthUntil('2026-10-01');

    $this->travelTo('2026-11-01 00:05:00');
    $this->artisan('game:tick')->assertSuccessful();

    expect(PerformanceReview::count())->toBe(3);
});

it('lets a dismissed player apply for a job again', function () {
    havePoorMonthUntil('2026-08-01');
    havePoorMonthUntil('2026-09-01');
    havePoorMonthUntil('2026-10-01');

    $opening = JobOpening::query()->where('slug', 'office-assistant-scheduling')->sole();

    expect(app(ApplicationEligibility::class)->refusalFor($this->player->fresh(), $opening))->toBeNull();
});

it('sends the dismissed player home with the letter in the private mailbox', function () {
    app(Dismissal::class)->dismiss($this->employment);
    $player = $this->player->fresh();

    $this->actingAs($player)->get(route('office'))->assertRedirect(route('home'));
    $this->actingAs($player)->get(route('home'))->assertInertia(fn ($page) => $page->where('player.employer', null));
    $this->actingAs($player)
        ->getJson(route('api.v1.emails.index', ['mailbox' => 'private']))
        ->assertJsonPath('data.0.subject', 'Your employment at Flowright Plumbing & Heating');
});
