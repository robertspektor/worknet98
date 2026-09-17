<?php

use App\FloppyDisks\DiskSource;
use App\Models\FloppyDisk;
use App\Models\PlayerFloppyDisk;
use App\Models\User;

it('hands out the starter disks with their files when the player opens the disk box', function () {
    $player = User::factory()->create(['locale' => 'en']);
    FloppyDisk::factory()->starter()->create(['slug' => 'welcome', 'color' => 'black']);
    FloppyDisk::factory()->starter()->program('minefield')->create(['slug' => 'minefield', 'color' => 'blue']);
    FloppyDisk::factory()->forSale()->create();

    $response = $this->actingAs($player)->getJson(route('api.v1.floppy-disks.index'))->assertOk();

    [$welcome, $minefield] = PlayerFloppyDisk::query()->orderBy('id')->get()->all();
    $response->assertExactJson(['data' => [
        ['id' => $welcome->id, 'slug' => 'welcome', 'kind' => 'data', 'color' => 'black', 'label' => null, 'is_labelable' => false, 'is_write_protected' => false, 'capacity_bytes' => 1_474_560],
        ['id' => $minefield->id, 'slug' => 'minefield', 'kind' => 'game', 'color' => 'blue', 'label' => null, 'is_labelable' => false, 'is_write_protected' => true, 'capacity_bytes' => 1_474_560],
    ]]);
    expect($minefield->source)->toBe(DiskSource::Starter)
        ->and($minefield->files()->where('name', 'SETUP.EXE')->sole()->program)->toBe('minefield');
});

it('hands out every starter disk only once', function () {
    $player = User::factory()->create();
    FloppyDisk::factory()->starter()->create();

    $this->actingAs($player)->getJson(route('api.v1.floppy-disks.index'))->assertJsonCount(1, 'data');
    $this->actingAs($player)->getJson(route('api.v1.floppy-disks.index'))->assertJsonCount(1, 'data');

    expect(PlayerFloppyDisk::count())->toBe(1);
});

it('keeps the disks of other players out of the disk box', function () {
    PlayerFloppyDisk::factory()->create();

    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.floppy-disks.index'))
        ->assertExactJson(['data' => []]);
});

it('requires a signed-in player to open the disk box', function () {
    $this->getJson(route('api.v1.floppy-disks.index'))->assertUnauthorized();
});
