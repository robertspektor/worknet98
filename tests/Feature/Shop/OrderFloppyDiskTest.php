<?php

use App\Models\FloppyDisk;
use App\Models\LedgerEntry;
use App\Models\Order;
use App\Models\User;
use App\Work\LedgerReason;
use App\Work\Wallet;
use Illuminate\Testing\TestResponse;

function orderDisk(User $player, FloppyDisk $disk): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.shop.floppy-disks.orders.store', $disk));
}

beforeEach(function () {
    $this->travelTo('2026-09-16 21:30:00');
});

it('charges the price and ships the disk on the next game day', function () {
    $player = playerWithCredits(100);
    $disk = FloppyDisk::factory()->forSale(40)->create();

    orderDisk($player, $disk)
        ->assertCreated()
        ->assertJsonPath('data.id', $disk->id)
        ->assertJsonPath('data.status', 'ordered')
        ->assertJsonPath('data.delivers_at', '2026-09-17T00:00:00+00:00');

    $order = Order::sole();
    expect($order->user_id)->toBe($player->id)
        ->and($order->price)->toBe(40)
        ->and(app(Wallet::class)->balanceOf($player))->toBe(60)
        ->and(LedgerEntry::query()->where('reason', LedgerReason::Purchase)->sole()->amount)->toBe(-40);
});

it('uses the configured delivery delay', function () {
    config(['game.parcel_delivery_delay_seconds' => 90]);

    orderDisk(playerWithCredits(100), FloppyDisk::factory()->forSale(40)->create())->assertCreated();

    expect(Order::sole()->delivers_at->equalTo(now()->addSeconds(90)))->toBeTrue();
});

it('refuses orders the player cannot afford', function () {
    $player = playerWithCredits(39);

    orderDisk($player, FloppyDisk::factory()->forSale(40)->create())
        ->assertUnprocessable()
        ->assertExactJson(['message' => 'Your account balance is too low for this purchase.', 'refusal' => 'insufficient_funds']);

    expect(Order::count())->toBe(0)
        ->and(app(Wallet::class)->balanceOf($player))->toBe(39);
});

it('refuses disks that are not for sale', function () {
    orderDisk(playerWithCredits(100), FloppyDisk::factory()->starter()->create())
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'not_for_sale');

    expect(Order::count())->toBe(0);
});

it('refuses a second order of the same disk without charging again', function () {
    $player = playerWithCredits(100);
    $disk = FloppyDisk::factory()->forSale(40)->create();
    orderDisk($player, $disk)->assertCreated();

    orderDisk($player, $disk)
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'already_ordered');

    expect(Order::count())->toBe(1)
        ->and(app(Wallet::class)->balanceOf($player))->toBe(60);
});

it('sells blank disks again once the previous pack is unpacked', function () {
    $player = playerWithCredits(100);
    $blankDisks = FloppyDisk::factory()->forSale(8)->blankPack(3)->create();
    orderDisk($player, $blankDisks)->assertCreated();

    orderDisk($player, $blankDisks)->assertUnprocessable()->assertJsonPath('refusal', 'already_ordered');

    Order::sole()->update(['delivered_at' => now(), 'unpacked_at' => now()]);

    orderDisk($player, $blankDisks)->assertCreated();
    expect(Order::count())->toBe(2)
        ->and(app(Wallet::class)->balanceOf($player))->toBe(84);
});

it('requires a signed-in player to order', function () {
    $this->postJson(route('api.v1.shop.floppy-disks.orders.store', FloppyDisk::factory()->forSale()->create()))->assertUnauthorized();
});
