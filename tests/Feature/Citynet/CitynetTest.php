<?php

use App\Auth\Role;
use App\Models\City;
use App\Models\Person;
use App\Models\User;
use App\Models\WorldEvent;
use App\World\Population\Occupation;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function () {
    $this->travelTo('2026-09-21 09:00:00');
    $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]);
    $this->mayor = User::factory()->create(['role' => Role::Mayor]);
});

it('keeps the terminal closed for players and guests', function () {
    $this->get(route('citynet'))->assertRedirect();

    $this->actingAs(User::factory()->create())->get(route('citynet'))->assertForbidden();
});

it('shows the mayor the figures of every city', function () {
    $millbrook = City::query()->where('slug', 'millbrook')->sole();
    $residents = Person::query()->whereBelongsTo($millbrook)->count();
    $unemployed = Person::query()->whereBelongsTo($millbrook)->where('occupation', Occupation::Unemployed)->count();

    $this->actingAs($this->mayor)
        ->get(route('citynet'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('citynet')
            ->where('mayor', 'Bill Rixx')
            ->where('cities.1.name', 'Millbrook')
            ->where('cities.1.residents', $residents)
            ->where('cities.1.unemployed', $unemployed)
            ->where('cities.0.name', 'Lindenstadt'));
});

it('lists the latest world events with the company that handled them and the event that caused them', function () {
    $hollis = Person::query()->whereRelation('city', 'slug', 'millbrook')->where('slug', 'margaret-hollis')->sole();
    $drip = WorldEvent::factory()->for($hollis)->create(['type' => 'dripping_pipe', 'key' => 'test|drip', 'occurred_at' => now()->subHour()]);
    WorldEvent::factory()->for($hollis)->create(['type' => 'burst_pipe', 'key' => 'test|burst', 'parent_id' => $drip->id, 'occurred_at' => now()]);

    $this->actingAs($this->mayor)
        ->get(route('citynet'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('events.0.type', 'burst_pipe')
            ->where('events.0.person', 'Margaret Hollis')
            ->where('events.0.city', 'Millbrook')
            ->where('events.0.caused_by', 'dripping_pipe')
            ->where('events.1.type', 'dripping_pipe')
            ->etc());
});

it('appoints a mayor from the command line', function () {
    $player = User::factory()->create();

    $this->artisan('mayor:appoint', ['email' => $player->email])->assertSuccessful();
    $this->artisan('mayor:appoint', ['email' => 'nobody@desklife98.test'])->assertFailed();

    expect($player->fresh()?->isMayor())->toBeTrue();
});
