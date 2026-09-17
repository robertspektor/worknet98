<?php

use App\Models\Email;
use App\Models\FloppyDisk;
use App\Models\HardwarePart;
use App\Models\Order;
use App\Models\User;

beforeEach(function () {
    $this->travelTo('2026-09-16 21:30:00');
});

function orderProduct(User $player, string $route, FloppyDisk|HardwarePart $product): void
{
    test()->actingAs($player)->postJson(route($route, $product))->assertCreated();
}

it('confirms a disk order by mail with the article, the price and the game delivery date', function () {
    $player = playerWithCredits(100);
    $disk = FloppyDisk::factory()->forSale(40)->create(['slug' => 'calculator']);

    orderProduct($player, 'api.v1.shop.floppy-disks.orders.store', $disk);

    $email = Email::sole();
    expect($email->user_id)->toBe($player->id)
        ->and($email->sender_name)->toBe('DiskDepot')
        ->and($email->sender_address)->toBe('orders@diskdepot.wn')
        ->and($email->subject)->toBe('Order DI-'.str_pad((string) Order::sole()->id, 6, '0', STR_PAD_LEFT).' confirmed: Calculator 1.2')
        ->and($email->body)->toContain('1x Calculator 1.2')
        ->and($email->body)->toContain('40 credits')
        ->and($email->body)->toContain('Delivery: Thursday, September 17, 2026, 00:00');
});

it('confirms a processor order in the player language', function () {
    $player = playerWithCredits(1000);
    $player->update(['locale' => 'de']);
    $part = HardwarePart::factory()->forSale(380)->create(['slug' => 'kalkulon-133']);

    orderProduct($player, 'api.v1.shop.hardware-parts.orders.store', $part);

    $email = Email::sole();
    expect($email->sender_name)->toBe('ChipCity')
        ->and($email->subject)->toContain('Bestellung CH-')
        ->and($email->subject)->toContain('Kalkulon 133MHz')
        ->and($email->body)->toContain('Abgebucht: 380 Credits')
        ->and($email->body)->toContain('Lieferung: Donnerstag, 17. September 2026, 00:00');
});

it('sends the confirmation before the parcel is on its way', function () {
    $player = playerWithCredits(100);

    orderProduct($player, 'api.v1.shop.floppy-disks.orders.store', FloppyDisk::factory()->forSale(40)->create());

    expect(Email::count())->toBe(1);

    $this->travel(1)->days();
    $this->artisan('shop:deliver-parcels')->assertSuccessful();

    expect(Email::count())->toBe(2)
        ->and(Email::query()->latest('id')->firstOrFail()->subject)->toStartWith('Your parcel has arrived');
});
