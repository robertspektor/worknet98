<?php

use App\Models\FloppyDisk;
use App\Models\Order;
use App\Models\User;

it('lists the disks for sale with their status for the player', function () {
    $player = User::factory()->create();
    $available = FloppyDisk::factory()->forSale(40)->create(['slug' => 'calculator']);
    $ordered = FloppyDisk::factory()->forSale(60)->create(['slug' => 'notepad']);
    $owned = FloppyDisk::factory()->forSale(80)->create(['slug' => 'paint']);
    FloppyDisk::factory()->starter()->create();
    Order::factory()->of($ordered)->create(['user_id' => $player->id, 'delivers_at' => '2026-09-17 00:00:00']);
    Order::factory()->of($owned)->unpacked()->create(['user_id' => $player->id]);
    Order::factory()->of($available)->create();

    $this->actingAs($player)
        ->getJson(route('api.v1.shop.floppy-disks.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.slug', 'calculator')
        ->assertJsonPath('data.0.price', 40)
        ->assertJsonPath('data.0.pack_size', 1)
        ->assertJsonPath('data.0.status', 'available')
        ->assertJsonPath('data.0.delivers_at', null)
        ->assertJsonPath('data.1.status', 'ordered')
        ->assertJsonPath('data.1.delivers_at', '2026-09-17T00:00:00+00:00')
        ->assertJsonPath('data.2.status', 'owned');
});

it('offers blank disks again once the last pack is unpacked', function () {
    $player = User::factory()->create();
    $blankDisks = FloppyDisk::factory()->forSale(8)->blankPack(3)->create();
    Order::factory()->of($blankDisks)->unpacked()->create(['user_id' => $player->id]);

    $this->actingAs($player)
        ->getJson(route('api.v1.shop.floppy-disks.index'))
        ->assertJsonPath('data.0.pack_size', 3)
        ->assertJsonPath('data.0.status', 'available');

    Order::factory()->of($blankDisks)->create(['user_id' => $player->id]);

    $this->actingAs($player)
        ->getJson(route('api.v1.shop.floppy-disks.index'))
        ->assertJsonPath('data.0.status', 'ordered');
});

it('requires a signed-in player to browse the shop', function () {
    $this->getJson(route('api.v1.shop.floppy-disks.index'))->assertUnauthorized();
});
