<?php

use App\Models\Employment;
use App\Models\Shift;
use App\Models\User;
use App\Work\Events\ShiftEnded;
use App\Work\Events\ShiftStarted;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    config(['game.clock' => [
        'scale' => 4.35,
        'real_epoch' => '2026-09-14 00:00:00',
        'game_epoch' => '1998-01-05 00:00:00',
    ]]);
    $this->travelTo('2026-09-17 10:00:00');
});

function employedPlayer(): User
{
    return User::findOrFail(Employment::factory()->create()->user_id);
}

function clockIn(User $player): void
{
    workAs($player, 'POST', 'api.v1.shift.clock-in');
}

function heartbeatAfter(User $player, int $seconds): void
{
    test()->travel($seconds)->seconds();
    workAs($player, 'POST', 'api.v1.shift.heartbeat');
}

it('starts a shift when the player clocks in', function () {
    Event::fake([ShiftStarted::class]);
    $player = employedPlayer();

    $this->actingAs($player)
        ->postJson(route('api.v1.shift.clock-in'))
        ->assertOk()
        ->assertJsonPath('data.status', 'on_duty')
        ->assertJsonPath('data.clocked_in_at', '1998-01-19T20:42:00')
        ->assertJsonPath('data.worked_seconds', 0)
        ->assertJsonPath('data.target_seconds', 7200);

    $shift = Shift::sole();
    expect($shift->user_id)->toBe($player->id)
        ->and($shift->employment_id)->toBe($player->employment?->id)
        ->and($shift->last_active_at->toDateTimeString())->toBe('2026-09-17 10:00:00');
    Event::assertDispatched(ShiftStarted::class, fn (ShiftStarted $event): bool => $event->shift->is($shift));
});

it('counts the active time reported by heartbeats', function () {
    $player = employedPlayer();
    clockIn($player);

    heartbeatAfter($player, 60);
    heartbeatAfter($player, 60);

    expect(Shift::sole()->worked_seconds)->toBe(120);
});

it('does not count a gap without heartbeats as work', function () {
    $player = employedPlayer();
    clockIn($player);
    heartbeatAfter($player, 60);

    heartbeatAfter($player, 400);
    heartbeatAfter($player, 60);

    expect(Shift::sole()->worked_seconds)->toBe(120);
});

it('counts the active time up to clocking out', function () {
    Event::fake([ShiftEnded::class]);
    $player = employedPlayer();
    clockIn($player);
    $this->travel(45)->seconds();

    $this->actingAs($player)
        ->postJson(route('api.v1.shift.clock-out'))
        ->assertOk()
        ->assertJsonPath('data.status', 'off_duty')
        ->assertJsonPath('data.worked_seconds', 45);

    expect(Shift::sole()->clocked_out_at?->toDateTimeString())->toBe('2026-09-17 10:00:45');
    Event::assertDispatched(ShiftEnded::class);
});

it('lets the player work several shifts in a contract period', function () {
    $player = employedPlayer();
    clockIn($player);
    heartbeatAfter($player, 60);
    workAs($player, 'POST', 'api.v1.shift.clock-out');

    $this->travel(2)->hours();
    clockIn($player);
    heartbeatAfter($player, 30);

    $this->actingAs($player)
        ->getJson(route('api.v1.shift.show'))
        ->assertJsonPath('data.status', 'on_duty')
        ->assertJsonPath('data.worked_seconds', 90);
    expect(Shift::count())->toBe(2);
});

it('describes the game month as the contract period', function () {
    $this->actingAs(employedPlayer())
        ->getJson(route('api.v1.shift.show'))
        ->assertJsonPath('data.status', 'off_duty')
        ->assertJsonPath('data.period.starts_on', '1998-01-01')
        ->assertJsonPath('data.period.ends_on', '1998-01-31')
        ->assertJsonPath('data.period.ends_at', '2026-09-20T04:57:56+00:00');
});

it('only counts shifts of the current contract period', function () {
    $player = employedPlayer();
    Shift::factory()->clockedOut()->create([
        'employment_id' => $player->employment?->id,
        'clocked_in_at' => '2026-09-13 01:55:51',
        'worked_seconds' => 3600,
    ]);
    Shift::factory()->clockedOut()->create([
        'employment_id' => $player->employment?->id,
        'clocked_in_at' => '2026-09-13 01:55:52',
        'worked_seconds' => 600,
    ]);

    $this->actingAs($player)->getJson(route('api.v1.shift.show'))->assertJsonPath('data.worked_seconds', 600);
});

it('keeps counting overtime beyond the target', function () {
    $player = employedPlayer();
    Shift::factory()->clockedOut()->create(['employment_id' => $player->employment?->id, 'worked_seconds' => 7200]);
    clockIn($player);
    heartbeatAfter($player, 60);

    $this->actingAs($player)->getJson(route('api.v1.shift.show'))->assertJsonPath('data.worked_seconds', 7260);
});

it('clocks out players who stopped being active', function () {
    Event::fake([ShiftEnded::class]);
    $player = employedPlayer();
    clockIn($player);
    heartbeatAfter($player, 60);

    $this->travel(15)->minutes();
    $this->travel(-1)->seconds();
    $this->artisan('game:tick')->assertSuccessful();
    expect(Shift::sole()->clocked_out_at)->toBeNull();

    $this->travel(1)->seconds();
    $this->artisan('game:tick')->assertSuccessful();

    $shift = Shift::sole();
    expect($shift->clocked_out_at?->toDateTimeString())->toBe('2026-09-17 10:01:00')
        ->and($shift->worked_seconds)->toBe(60)
        ->and($shift->clocked_out_automatically)->toBeTrue();
    Event::assertDispatched(ShiftEnded::class);

    $this->actingAs($player)
        ->getJson(route('api.v1.shift.show'))
        ->assertJsonPath('data.status', 'off_duty')
        ->assertJsonPath('data.clocked_out_automatically', true);
});

it('forgets the automatic clock out notice once the player clocks in again', function () {
    $player = employedPlayer();
    Shift::factory()->clockedOut()->create(['employment_id' => $player->employment?->id, 'clocked_out_automatically' => true]);

    $this->travel(1)->minutes();
    clockIn($player);

    $this->actingAs($player)->getJson(route('api.v1.shift.show'))->assertJsonPath('data.clocked_out_automatically', false);
});

it('refuses to clock in twice', function () {
    $player = employedPlayer();
    Shift::factory()->create(['employment_id' => $player->employment?->id]);

    $this->actingAs($player)
        ->postJson(route('api.v1.shift.clock-in'))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'already_on_duty');
});

it('refuses to clock out or send heartbeats without a running shift', function (string $route) {
    $this->actingAs(employedPlayer())
        ->postJson(route($route))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'not_on_duty');
})->with(['api.v1.shift.clock-out', 'api.v1.shift.heartbeat']);

it('refuses shifts for unemployed players', function () {
    $this->actingAs(User::factory()->create())
        ->postJson(route('api.v1.shift.clock-in'))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'not_employed');
});
