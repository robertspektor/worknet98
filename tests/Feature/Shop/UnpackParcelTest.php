<?php

use App\Models\FloppyDisk;
use App\Models\FloppyDiskOrder;
use App\Models\User;
use Illuminate\Testing\TestResponse;

function unpackParcel(User $player, FloppyDiskOrder $order): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.parcels.unpacking.store', $order));
}

it('lists the parcels waiting on the desk', function () {
    $player = User::factory()->create();
    $waiting = FloppyDiskOrder::factory()->delivered()->create(['user_id' => $player->id]);
    FloppyDiskOrder::factory()->create(['user_id' => $player->id]);
    FloppyDiskOrder::factory()->unpacked()->create(['user_id' => $player->id]);
    FloppyDiskOrder::factory()->delivered()->create();

    $this->actingAs($player)
        ->getJson(route('api.v1.parcels.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $waiting->id)
        ->assertJsonPath('data.0.floppy_disk.id', $waiting->floppy_disk_id);
});

it('moves the disk of an unpacked parcel into the disk box', function () {
    $this->freezeSecond();
    $order = FloppyDiskOrder::factory()->delivered()->create();
    $starter = FloppyDisk::factory()->starter()->create();

    unpackParcel($order->user, $order)->assertNoContent();

    expect($order->fresh()?->unpacked_at?->equalTo(now()))->toBeTrue();
    $this->actingAs($order->user)
        ->getJson(route('api.v1.floppy-disks.index'))
        ->assertJsonPath('data.*.id', [$order->floppy_disk_id, $starter->id]);
});

it('does not put ordered disks into other players boxes', function () {
    FloppyDiskOrder::factory()->unpacked()->create();

    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.floppy-disks.index'))
        ->assertJsonCount(0, 'data');
});

it('refuses to unpack parcels that have not arrived', function () {
    $order = FloppyDiskOrder::factory()->create();

    unpackParcel($order->user, $order)->assertForbidden();

    expect($order->fresh()?->unpacked_at)->toBeNull();
});

it('refuses to unpack the parcels of other players', function () {
    $order = FloppyDiskOrder::factory()->delivered()->create();

    unpackParcel(User::factory()->create(), $order)->assertForbidden();
});
