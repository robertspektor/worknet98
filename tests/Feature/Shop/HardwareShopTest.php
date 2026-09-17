<?php

use App\Models\Email;
use App\Models\HardwarePart;
use App\Models\Order;
use App\Models\PlayerHardwarePart;
use App\Models\User;
use App\Work\Wallet;
use Illuminate\Testing\TestResponse;

function orderHardware(User $player, HardwarePart $part): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.shop.hardware-parts.orders.store', $part));
}

beforeEach(function () {
    HardwarePart::factory()->starter(75)->create(['slug' => 'kalkulon-75']);
});

it('lists the processors for sale at ChipCity without the starter part', function () {
    $player = User::factory()->create();
    $turbo = HardwarePart::factory()->forSale(900)->create(['slug' => 'kalkulon-200-turbo', 'speed_mhz' => 200]);
    HardwarePart::factory()->forSale(380)->create(['slug' => 'kalkulon-133']);
    Order::factory()->of($turbo)->create(['user_id' => $player->id]);

    $this->actingAs($player)
        ->getJson(route('api.v1.shop.hardware-parts.index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.slug', 'kalkulon-133')
        ->assertJsonPath('data.0.status', 'available')
        ->assertJsonPath('data.1.speed_mhz', 200)
        ->assertJsonPath('data.1.price', 900)
        ->assertJsonPath('data.1.status', 'ordered');
});

it('charges a processor order and ships it with a ChipCity mail in the player language', function () {
    $player = playerWithCredits(1000);
    $player->update(['locale' => 'de']);
    $turbo = HardwarePart::factory()->forSale(900)->create(['slug' => 'kalkulon-200-turbo']);

    orderHardware($player, $turbo)->assertCreated()->assertJsonPath('data.status', 'ordered');
    $this->travel(1)->days();
    $this->artisan('shop:deliver-parcels')->assertSuccessful();

    $email = Email::sole();
    expect(app(Wallet::class)->balanceOf($player))->toBe(100)
        ->and($email->sender_address)->toBe('orders@chipcity.wn')
        ->and($email->subject)->toBe('Mit Sorgfalt verschickt: Kalkulon 200MHz Turbo')
        ->and($email->body)->toContain('900 Credits');
});

it('refuses to sell the same processor twice', function () {
    $player = playerWithCredits(2000);
    $part = HardwarePart::factory()->forSale(380)->create();

    orderHardware($player, $part)->assertCreated();
    orderHardware($player, $part)->assertUnprocessable()->assertJsonPath('message', 'You already ordered this item.');
});

it('refuses to sell the starter processor', function () {
    orderHardware(playerWithCredits(100), HardwarePart::query()->starter()->sole())->assertUnprocessable();
});

it('puts an unpacked processor on the desk, exactly once', function () {
    $player = User::factory()->create();
    $part = HardwarePart::factory()->forSale()->create(['slug' => 'kalkulon-133', 'speed_mhz' => 133]);
    $order = Order::factory()->of($part)->delivered()->create(['user_id' => $player->id]);

    $this->actingAs($player)->getJson(route('api.v1.parcels.index'))
        ->assertJsonPath('data.0.storefront', 'chipcity')
        ->assertJsonPath('data.0.product.type', 'hardware_part')
        ->assertJsonPath('data.0.product.speed_mhz', 133);

    $this->actingAs($player)->postJson(route('api.v1.parcels.unpacking.store', $order))->assertNoContent();
    $this->actingAs($player)->postJson(route('api.v1.parcels.unpacking.store', $order))->assertNoContent();

    expect(PlayerHardwarePart::count())->toBe(1);
    $this->actingAs($player)
        ->getJson(route('api.v1.home-computer.show'))
        ->assertJsonPath('data.cpu.slug', 'kalkulon-75')
        ->assertJsonPath('data.desk_parts', [
            ['id' => PlayerHardwarePart::sole()->id, 'slug' => 'kalkulon-133', 'slot' => 'cpu', 'speed_mhz' => 133, 'is_used' => false],
        ]);
});

it('does not put unpacked floppy disks on the desk', function () {
    $order = Order::factory()->delivered()->create();

    $this->actingAs($order->user)->postJson(route('api.v1.parcels.unpacking.store', $order))->assertNoContent();

    expect(PlayerHardwarePart::count())->toBe(0);
});
