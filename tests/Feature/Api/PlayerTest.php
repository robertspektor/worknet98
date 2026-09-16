<?php

use App\Models\User;

it('returns the signed-in player', function () {
    $player = User::factory()->locale('de')->create(['email' => 'gary@example.com']);

    $this->actingAs($player)
        ->getJson(route('api.v1.player.show'))
        ->assertOk()
        ->assertExactJson(['data' => ['email' => 'gary@example.com', 'locale' => 'de', 'employer' => null]]);
});

it('requires a signed-in player', function () {
    $this->getJson(route('api.v1.player.show'))->assertUnauthorized();
});
