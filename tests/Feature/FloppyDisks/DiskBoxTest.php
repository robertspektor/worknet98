<?php

use App\Models\FloppyDisk;
use App\Models\User;

it('lists the starter disks in the disk box of a player', function () {
    $welcome = FloppyDisk::factory()->starter()->create(['slug' => 'welcome', 'color' => 'black']);
    $game = FloppyDisk::factory()->starter()->program('minefield')->create(['slug' => 'minefield', 'color' => 'blue']);
    FloppyDisk::factory()->create();

    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.floppy-disks.index'))
        ->assertOk()
        ->assertExactJson(['data' => [
            ['id' => $welcome->id, 'slug' => 'welcome', 'kind' => 'data', 'color' => 'black', 'program' => null],
            ['id' => $game->id, 'slug' => 'minefield', 'kind' => 'game', 'color' => 'blue', 'program' => 'minefield'],
        ]]);
});

it('requires a signed-in player to open the disk box', function () {
    $this->getJson(route('api.v1.floppy-disks.index'))->assertUnauthorized();
});
