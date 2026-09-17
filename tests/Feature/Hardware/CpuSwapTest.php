<?php

use App\Models\HardwarePart;
use App\Models\PlayerHardwarePart;
use App\Models\User;
use Illuminate\Testing\TestResponse;

function installDeskPart(User $player, PlayerHardwarePart $deskPart, bool $thermalPasteApplied = true): TestResponse
{
    return test()->actingAs($player)->postJson(
        route('api.v1.home-computer.desk-parts.installation.store', $deskPart),
        ['thermal_paste_applied' => $thermalPasteApplied],
    );
}

function repaste(User $player): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.home-computer.thermal-paste.store'));
}

function newCpuOnDesk(User $player, string $slug = 'kalkulon-133', int $speedMhz = 133): PlayerHardwarePart
{
    $part = HardwarePart::factory()->forSale()->create(['slug' => $slug, 'speed_mhz' => $speedMhz]);

    return PlayerHardwarePart::factory()->create(['user_id' => $player->id, 'hardware_part_id' => $part->id]);
}

beforeEach(function () {
    $this->freezeSecond();
    HardwarePart::factory()->starter(75)->create(['slug' => 'kalkulon-75']);
});

it('swaps the starter processor for the new one and leaves the old one on the desk', function () {
    $player = User::factory()->create();
    $deskPart = newCpuOnDesk($player);

    installDeskPart($player, $deskPart)
        ->assertOk()
        ->assertJsonPath('data.cpu.slug', 'kalkulon-133')
        ->assertJsonPath('data.cpu.speed_mhz', 133)
        ->assertJsonPath('data.cpu.needs_thermal_paste', false)
        ->assertJsonPath('data.desk_parts.0.slug', 'kalkulon-75')
        ->assertJsonPath('data.desk_parts.0.is_used', true)
        ->assertJsonCount(1, 'data.desk_parts');
});

it('removes a previously installed upgrade when the next one goes in', function () {
    $player = User::factory()->create();
    installDeskPart($player, newCpuOnDesk($player))->assertOk();

    installDeskPart($player, newCpuOnDesk($player, 'kalkulon-200-turbo', 200))
        ->assertOk()
        ->assertJsonPath('data.cpu.slug', 'kalkulon-200-turbo')
        ->assertJsonPath('data.desk_parts.*.slug', ['kalkulon-133', 'kalkulon-75']);

    expect(PlayerHardwarePart::query()->where('user_id', $player->id)->installed()->count())->toBe(1);
});

it('can put an old processor back in', function () {
    $player = User::factory()->create();
    installDeskPart($player, newCpuOnDesk($player))->assertOk();
    $starterOnDesk = PlayerHardwarePart::query()->whereRelation('hardwarePart', 'slug', 'kalkulon-75')->sole();

    installDeskPart($player, $starterOnDesk)
        ->assertOk()
        ->assertJsonPath('data.cpu.slug', 'kalkulon-75')
        ->assertJsonPath('data.desk_parts.0.slug', 'kalkulon-133');
});

it('runs hot until thermal paste is applied', function () {
    $player = User::factory()->create();

    installDeskPart($player, newCpuOnDesk($player), thermalPasteApplied: false)
        ->assertJsonPath('data.cpu.needs_thermal_paste', true);

    repaste($player)->assertOk()->assertJsonPath('data.cpu.needs_thermal_paste', false);
});

it('refuses to repaste a processor that is not running hot', function () {
    repaste(User::factory()->create())
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'thermal_paste_not_needed');
});

it('refuses to install parts of other players or parts that are already installed', function () {
    $player = User::factory()->create();
    $deskPart = newCpuOnDesk($player);

    installDeskPart(User::factory()->create(), $deskPart)->assertForbidden();

    installDeskPart($player, $deskPart)->assertOk();
    installDeskPart($player, $deskPart->fresh())->assertForbidden();
});

it('requires to say whether thermal paste was applied', function () {
    $player = User::factory()->create();

    $this->actingAs($player)
        ->postJson(route('api.v1.home-computer.desk-parts.installation.store', newCpuOnDesk($player)))
        ->assertJsonValidationErrors('thermal_paste_applied');
});
