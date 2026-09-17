<?php

use App\Models\HardwarePart;
use App\Models\PlayerHardwarePart;
use App\Models\User;
use Database\Seeders\HardwarePartSeeder;

beforeEach(function () {
    HardwarePart::factory()->starter(75)->create(['slug' => 'kalkulon-75']);
});

it('runs a new player on the slow starter processor', function () {
    $this->actingAs(User::factory()->create())
        ->getJson(route('api.v1.home-computer.show'))
        ->assertOk()
        ->assertExactJson(['data' => ['cpu' => ['slug' => 'kalkulon-75', 'speed_mhz' => 75, 'needs_thermal_paste' => false], 'desk_parts' => []]]);
});

it('runs on the installed processor', function () {
    $player = User::factory()->create();
    $turbo = HardwarePart::factory()->forSale()->create(['slug' => 'kalkulon-200-turbo', 'speed_mhz' => 200]);
    PlayerHardwarePart::factory()->installed()->withoutThermalPaste()->create(['user_id' => $player->id, 'hardware_part_id' => $turbo->id]);

    $this->actingAs($player)
        ->getJson(route('api.v1.home-computer.show'))
        ->assertOk()
        ->assertJsonPath('data.cpu.slug', 'kalkulon-200-turbo')
        ->assertJsonPath('data.cpu.speed_mhz', 200)
        ->assertJsonPath('data.cpu.needs_thermal_paste', true);
});

it('ignores processors lying on the desk and other players parts', function () {
    $player = User::factory()->create();
    PlayerHardwarePart::factory()->create(['user_id' => $player->id]);
    PlayerHardwarePart::factory()->installed()->create();

    $this->actingAs($player)
        ->getJson(route('api.v1.home-computer.show'))
        ->assertJsonPath('data.cpu.slug', 'kalkulon-75');
});

it('requires a signed-in player', function () {
    $this->getJson(route('api.v1.home-computer.show'))->assertUnauthorized();
});

it('seeds exactly one starter processor, repeatedly without duplicates', function () {
    HardwarePart::query()->delete();

    $this->seed(HardwarePartSeeder::class);
    $this->seed(HardwarePartSeeder::class);

    expect(HardwarePart::query()->starter()->count())->toBe(1)
        ->and(HardwarePart::query()->whereNotNull('price')->count())->toBe(HardwarePart::count() - 1);
});
