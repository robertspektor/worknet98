<?php

use App\Models\Employment;
use App\Models\LedgerEntry;
use App\Models\Shift;
use App\Models\User;
use App\Work\Events\ShiftEnded;
use App\Work\Events\ShiftStarted;
use App\Work\LedgerReason;
use Illuminate\Support\Facades\Event;

beforeEach(fn () => $this->freezeSecond());

function employedPlayer(int $dailySalary = 95): User
{
    $employment = Employment::factory()->create(['daily_salary' => $dailySalary]);

    return User::findOrFail($employment->user_id);
}

it('starts a shift when the player clocks in', function () {
    Event::fake([ShiftStarted::class]);
    $player = employedPlayer();

    $this->actingAs($player)
        ->postJson(route('api.v1.shift.clock-in'))
        ->assertOk()
        ->assertJsonPath('data.status', 'on_duty')
        ->assertJsonPath('data.daily_salary', 95)
        ->assertJsonPath('data.clocked_in_at', now()->toIso8601String());

    $shift = Shift::sole();
    expect($shift->user_id)->toBe($player->id)
        ->and($shift->employment_id)->toBe($player->employment?->id)
        ->and($shift->work_date->toDateString())->toBe(now()->toDateString());
    Event::assertDispatched(ShiftStarted::class, fn (ShiftStarted $event): bool => $event->shift->is($shift));
});

it('pays the daily salary when the player clocks out', function () {
    Event::fake([ShiftEnded::class]);
    $player = employedPlayer(dailySalary: 95);
    $this->actingAs($player)->postJson(route('api.v1.shift.clock-in'));

    $this->actingAs($player)
        ->postJson(route('api.v1.shift.clock-out'))
        ->assertOk()
        ->assertJsonPath('data.status', 'done');

    $entry = LedgerEntry::sole();
    expect($entry->user_id)->toBe($player->id)
        ->and($entry->amount)->toBe(95)
        ->and($entry->reason)->toBe(LedgerReason::Salary)
        ->and($entry->shift_id)->toBe(Shift::sole()->id);
    Event::assertDispatched(ShiftEnded::class);

    $this->actingAs($player)->getJson(route('api.v1.player.show'))->assertJsonPath('data.balance', 95);
});

it('allows one shift per day', function () {
    $player = employedPlayer();
    Shift::factory()->clockedOut()->create(['employment_id' => $player->employment?->id]);

    $this->actingAs($player)
        ->postJson(route('api.v1.shift.clock-in'))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'shift_done');
});

it('starts a new shift on the next day', function () {
    $player = employedPlayer();
    Shift::factory()->clockedOut()->create(['employment_id' => $player->employment?->id, 'work_date' => now()->subDay()->toDateString()]);

    $this->actingAs($player)->postJson(route('api.v1.shift.clock-in'))->assertOk();

    expect(Shift::count())->toBe(2);
});

it('refuses to clock in twice', function () {
    $player = employedPlayer();
    Shift::factory()->create(['employment_id' => $player->employment?->id]);

    $this->actingAs($player)
        ->postJson(route('api.v1.shift.clock-in'))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'already_on_duty');
});

it('refuses to clock out without a running shift', function () {
    $this->actingAs(employedPlayer())
        ->postJson(route('api.v1.shift.clock-out'))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'not_on_duty');

    expect(LedgerEntry::count())->toBe(0);
});

it('refuses shifts for unemployed players', function () {
    $this->actingAs(User::factory()->create())
        ->postJson(route('api.v1.shift.clock-in'))
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'not_employed');
});

it('shows the shift of today', function () {
    $player = employedPlayer();

    $this->actingAs($player)->getJson(route('api.v1.shift.show'))->assertJsonPath('data.status', 'off_duty');

    Shift::factory()->create(['employment_id' => $player->employment?->id]);

    $this->actingAs($player)->getJson(route('api.v1.shift.show'))->assertJsonPath('data.status', 'on_duty');
});
