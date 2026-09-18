<?php

use App\Metrics\Funnel;
use App\Metrics\FunnelRow;
use App\Metrics\FunnelStep;
use App\Metrics\ReturnRate;
use App\Metrics\SessionSummary;
use App\Models\PlayerEvent;
use App\Models\PlayerSession;
use App\Models\User;

function playerSession(User $player, string $startedAt, int $minutes): PlayerSession
{
    return PlayerSession::create([
        'user_id' => $player->id,
        'started_at' => $startedAt,
        'last_seen_at' => now()->parse($startedAt)->addMinutes($minutes),
    ]);
}

function funnelRow(string $label): FunnelRow
{
    return collect(app(Funnel::class)->steps())->firstOrFail(fn (FunnelRow $row): bool => $row->label === $label);
}

beforeEach(function () {
    $this->travelTo('2026-09-23 09:00:00');
});

it('starts a new session only after a gap and extends the running one otherwise', function () {
    $player = User::factory()->create();

    $this->actingAs($player)->getJson(route('api.v1.player.show'))->assertOk();
    $this->travel(20)->minutes();
    $this->actingAs($player)->getJson(route('api.v1.player.show'))->assertOk();

    expect(PlayerSession::query()->count())->toBe(1)
        ->and(PlayerSession::query()->sole()->lengthInMinutes())->toBe(20);

    $this->travel(40)->minutes();
    $this->actingAs($player)->getJson(route('api.v1.player.show'))->assertOk();

    expect(PlayerSession::query()->count())->toBe(2);
});

it('counts a player as returned when they come back the day after their first session', function () {
    $stayed = User::factory()->create();
    playerSession($stayed, '2026-09-20 10:00:00', 15);
    playerSession($stayed, '2026-09-21 18:00:00', 20);

    $left = User::factory()->create();
    playerSession($left, '2026-09-20 10:00:00', 5);

    $tooNew = User::factory()->create();
    playerSession($tooNew, '2026-09-23 08:00:00', 5);

    $dayTwo = app(ReturnRate::class)->dayTwo();

    expect($dayTwo->eligible)->toBe(2)
        ->and($dayTwo->returned)->toBe(1)
        ->and($dayTwo->percentage())->toBe(50);
});

it('summarises session count, median and longest length', function () {
    $player = User::factory()->create();
    playerSession($player, '2026-09-22 10:00:00', 5);
    playerSession($player, '2026-09-22 12:00:00', 20);
    playerSession($player, '2026-09-22 14:00:00', 60);
    playerSession(User::factory()->create(), '2026-01-01 10:00:00', 900);

    $lengths = app(SessionSummary::class)->sinceDays(30);

    expect($lengths->sessions)->toBe(3)
        ->and($lengths->players)->toBe(1)
        ->and($lengths->medianMinutes)->toBe(20)
        ->and($lengths->longestMinutes)->toBe(60)
        ->and($lengths->perPlayer())->toBe(3.0);
});

it('records a funnel step once however often the player repeats it', function () {
    $player = User::factory()->create();

    $this->actingAs($player)->postJson(route('api.v1.metrics.desktop-reached.store'))->assertNoContent();
    $this->actingAs($player)->postJson(route('api.v1.metrics.desktop-reached.store'))->assertNoContent();

    expect(PlayerEvent::query()->where('user_id', $player->id)->count())->toBe(1)
        ->and(PlayerEvent::query()->sole()->name)->toBe(FunnelStep::DesktopReached);
});

it('records that the player opened WorkNet when the job board is listed', function () {
    $player = User::factory()->create();

    $this->actingAs($player)->getJson(route('api.v1.job-openings.index'))->assertOk();

    expect(funnelRow('worknet_opened')->players)->toBe(1);
});

it('runs the funnel from registration through the steps into the milestone ladder', function () {
    $player = User::factory()->create();
    User::factory()->create();
    PlayerEvent::create(['user_id' => $player->id, 'name' => FunnelStep::DesktopReached, 'recorded_at' => now()]);

    expect(funnelRow('registered')->players)->toBe(2)
        ->and(funnelRow('desktop_reached'))->toMatchObject(['players' => 1, 'lost' => 1])
        ->and(funnelRow('application_sent')->players)->toBe(0)
        ->and(funnelRow('first_job')->players)->toBe(0)
        ->and(funnelRow('savings_50000')->players)->toBe(0);
});

it('prints the report without a single player in the database', function () {
    $this->artisan('metrics:report')->assertSuccessful();
});
