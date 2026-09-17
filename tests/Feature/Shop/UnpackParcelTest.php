<?php

use App\FloppyDisks\DiskSource;
use App\Models\FloppyDisk;
use App\Models\Order;
use App\Models\PlayerFloppyDisk;
use App\Models\User;
use Illuminate\Testing\TestResponse;

function unpackParcel(User $player, Order $order): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.parcels.unpacking.store', $order));
}

it('lists the parcels waiting on the desk', function () {
    $player = User::factory()->create();
    $waiting = Order::factory()->delivered()->create(['user_id' => $player->id]);
    Order::factory()->create(['user_id' => $player->id]);
    Order::factory()->unpacked()->create(['user_id' => $player->id]);
    Order::factory()->delivered()->create();

    $this->actingAs($player)
        ->getJson(route('api.v1.parcels.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $waiting->id)
        ->assertJsonPath('data.0.storefront', 'diskdepot')
        ->assertJsonPath('data.0.product.type', 'floppy_disk')
        ->assertJsonPath('data.0.product.id', $waiting->product_id);
});

it('moves the disk of an unpacked parcel into the disk box', function () {
    $this->freezeSecond();
    $disk = FloppyDisk::factory()->program('calculator')->create(['slug' => 'calculator']);
    $order = Order::factory()->of($disk)->delivered()->create();
    FloppyDisk::factory()->starter()->create(['slug' => 'welcome']);

    unpackParcel($order->user, $order)->assertNoContent();

    expect($order->fresh()?->unpacked_at?->equalTo(now()))->toBeTrue();
    $this->actingAs($order->user)
        ->getJson(route('api.v1.floppy-disks.index'))
        ->assertJsonPath('data.*.slug', ['calculator', 'welcome']);
    $bought = PlayerFloppyDisk::query()->where('source', DiskSource::Shop)->sole();
    expect($bought->order_id)->toBe($order->id)
        ->and($bought->is_write_protected)->toBeTrue()
        ->and($bought->files()->orderBy('name')->pluck('name')->all())->toBe(['README.TXT', 'SETUP.EXE']);
});

it('puts every disk of an unpacked blank disk pack into the disk box, exactly once', function () {
    $order = Order::factory()->of(FloppyDisk::factory()->forSale(8)->blankPack(3)->create())->delivered()->create();

    unpackParcel($order->user, $order)->assertNoContent();
    unpackParcel($order->user, $order)->assertNoContent();

    $disks = PlayerFloppyDisk::query()->where('source', DiskSource::Shop)->get();
    expect($disks)->toHaveCount(3)
        ->and($disks->every(fn (PlayerFloppyDisk $disk): bool => $disk->user_id === $order->user_id && ! $disk->is_write_protected))->toBeTrue();
});

it('does not put ordered disks into other players boxes', function () {
    Order::factory()->unpacked()->create();

    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.floppy-disks.index'))
        ->assertJsonCount(0, 'data');
});

it('refuses to unpack parcels that have not arrived', function () {
    $order = Order::factory()->create();

    unpackParcel($order->user, $order)->assertForbidden();

    expect($order->fresh()?->unpacked_at)->toBeNull();
});

it('refuses to unpack the parcels of other players', function () {
    $order = Order::factory()->delivered()->create();

    unpackParcel(User::factory()->create(), $order)->assertForbidden();
});
