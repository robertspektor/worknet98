<?php

use App\Models\City;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('shows whoever is signed in which address and city the city has on file', function () {
    City::factory()->create(['locale' => 'en', 'name' => 'Millbrook']);
    $player = User::factory()->locale('en')->create(['email' => 'gary@example.com']);

    $this->actingAs($player)
        ->get(route('terminal'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('terminal')
            ->where('address', 'gary@example.com')
            ->where('city', 'Millbrook'));
});

it('keeps the terminal to residents', function () {
    $this->get(route('terminal'))->assertRedirect(route('landing'));
    $this->post(route('terminal.leave'))->assertRedirect(route('landing'));
});

it('sends a player who just registered home as an arrival', function () {
    $this->post(route('sign-in.store'), [
        'email' => 'new@example.com',
        'age_confirmed' => '1',
        'locale' => 'en',
    ])->assertRedirect(route('terminal'));

    $this->get(route('terminal'))->assertOk();

    $this->post(route('terminal.leave'))
        ->assertRedirect(route('home'))
        ->assertSessionHas('status', 'arriving');
});

it('sends a player who signed in home as a return', function () {
    $player = User::factory()->locale('en')->create();

    $this->actingAs($player)
        ->withSession(['status' => 'signed-in'])
        ->get(route('terminal'))
        ->assertOk();

    $this->post(route('terminal.leave'))
        ->assertRedirect(route('home'))
        ->assertSessionHas('status', 'returning');
});

it('tells the home page which city the player came from', function () {
    City::factory()->create(['locale' => 'de', 'name' => 'Lindenstadt']);

    $this->actingAs(User::factory()->locale('de')->create())
        ->get(route('home'))
        ->assertInertia(fn (AssertableInertia $page) => $page->where('city', 'Lindenstadt'));
});
