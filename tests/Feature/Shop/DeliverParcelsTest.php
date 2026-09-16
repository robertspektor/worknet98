<?php

use App\Models\Email;
use App\Models\FloppyDisk;
use App\Models\FloppyDiskOrder;
use App\Models\User;

it('delivers due parcels with a shipping mail in the player language', function () {
    $this->freezeSecond();
    $player = User::factory()->locale('de')->create();
    $disk = FloppyDisk::factory()->forSale(40)->create(['slug' => 'calculator']);
    $order = FloppyDiskOrder::factory()->due()->create(['user_id' => $player->id, 'floppy_disk_id' => $disk->id, 'price' => 40]);

    $this->artisan('shop:deliver-parcels')->assertSuccessful();

    expect($order->fresh()?->delivered_at?->equalTo(now()))->toBeTrue();
    $email = Email::sole();
    expect($email->user_id)->toBe($player->id)
        ->and($email->sender_address)->toBe('orders@diskdepot.wn')
        ->and($email->subject)->toBe('Ihr Paket ist da: Taschenrechner 1.2')
        ->and($email->body)->toContain('40 Credits');
});

it('keeps parcels in transit until their delivery time', function () {
    $order = FloppyDiskOrder::factory()->create(['delivers_at' => now()->addMinute()]);

    $this->artisan('shop:deliver-parcels')->assertSuccessful();

    expect($order->fresh()?->delivered_at)->toBeNull()
        ->and(Email::count())->toBe(0);
});

it('delivers every parcel only once', function () {
    FloppyDiskOrder::factory()->due()->create();

    $this->artisan('shop:deliver-parcels')->assertSuccessful();
    $this->artisan('shop:deliver-parcels')->assertSuccessful();

    expect(Email::count())->toBe(1);
});
