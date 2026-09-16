<?php

use App\Models\FloppyDisk;
use App\Models\FloppyDiskOrder;
use App\Models\User;

it('lists the disks for sale with their status for the player', function () {
    $player = User::factory()->create();
    $available = FloppyDisk::factory()->forSale(40)->program('calculator')->create(['slug' => 'calculator']);
    $ordered = FloppyDisk::factory()->forSale(60)->create(['slug' => 'notepad']);
    $owned = FloppyDisk::factory()->forSale(80)->create(['slug' => 'paint']);
    FloppyDisk::factory()->starter()->create();
    FloppyDiskOrder::factory()->create(['user_id' => $player->id, 'floppy_disk_id' => $ordered->id, 'delivers_at' => '2026-09-17 00:00:00']);
    FloppyDiskOrder::factory()->unpacked()->create(['user_id' => $player->id, 'floppy_disk_id' => $owned->id]);
    FloppyDiskOrder::factory()->create(['floppy_disk_id' => $available->id]);

    $this->actingAs($player)
        ->getJson(route('api.v1.shop.floppy-disks.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.slug', 'calculator')
        ->assertJsonPath('data.0.price', 40)
        ->assertJsonPath('data.0.program', 'calculator')
        ->assertJsonPath('data.0.status', 'available')
        ->assertJsonPath('data.0.delivers_at', null)
        ->assertJsonPath('data.1.status', 'ordered')
        ->assertJsonPath('data.1.delivers_at', '2026-09-17T00:00:00+00:00')
        ->assertJsonPath('data.2.status', 'owned');
});

it('requires a signed-in player to browse the shop', function () {
    $this->getJson(route('api.v1.shop.floppy-disks.index'))->assertUnauthorized();
});
