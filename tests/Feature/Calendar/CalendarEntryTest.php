<?php

use App\Models\CalendarEntry;
use App\Models\User;

beforeEach(fn () => $this->travelTo('2026-09-18 09:00:00'));

it('adds an entry to the calendar of the player', function () {
    $player = User::factory()->create();

    $this->actingAs($player)
        ->postJson(route('api.v1.calendar-entries.store'), ['date' => '2026-09-21', 'time' => '10:00', 'title' => ' Mrs. Hollis, sink '])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Mrs. Hollis, sink');

    $entry = CalendarEntry::sole();
    expect($entry->user_id)->toBe($player->id)
        ->and($entry->date->toDateString())->toBe('2026-09-21')
        ->and($entry->time)->toBe('10:00');
});

it('lists upcoming entries in order', function () {
    $player = User::factory()->create();
    CalendarEntry::factory()->for($player)->create(['date' => '2026-09-22', 'time' => '08:00', 'title' => 'Later']);
    CalendarEntry::factory()->for($player)->create(['date' => '2026-09-21', 'time' => '15:00', 'title' => 'Sooner']);
    CalendarEntry::factory()->for($player)->create(['date' => '2026-09-17', 'title' => 'Past']);
    CalendarEntry::factory()->create(['date' => '2026-09-21', 'title' => 'Foreign']);

    $titles = $this->actingAs($player)->getJson(route('api.v1.calendar-entries.index'))->json('data.*.title');

    expect($titles)->toBe(['Sooner', 'Later']);
});

it('validates calendar entries', function () {
    $this->actingAs(User::factory()->create())
        ->postJson(route('api.v1.calendar-entries.store'), ['date' => '2026-09-17', 'time' => '25:00', 'title' => ''])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['date', 'time', 'title']);
});

it('removes only own entries', function () {
    $player = User::factory()->create();
    $own = CalendarEntry::factory()->for($player)->create();
    $foreign = CalendarEntry::factory()->create();

    $this->actingAs($player)->deleteJson(route('api.v1.calendar-entries.destroy', $own))->assertNoContent();
    $this->actingAs($player)->deleteJson(route('api.v1.calendar-entries.destroy', $foreign))->assertForbidden();

    expect(CalendarEntry::pluck('id')->all())->toBe([$foreign->id]);
});
