<?php

use App\Http\Requests\LabelFloppyDiskRequest;
use App\Models\PlayerFloppyDisk;
use App\Models\User;
use Illuminate\Testing\TestResponse;

function labelDisk(User $player, PlayerFloppyDisk $disk, ?string $label): TestResponse
{
    return test()->actingAs($player)->putJson(route('api.v1.floppy-disks.label.update', $disk), ['label' => $label]);
}

it('writes a label on a blank disk', function () {
    $player = User::factory()->create();
    $disk = PlayerFloppyDisk::factory()->blank()->create(['user_id' => $player->id]);

    labelDisk($player, $disk, '  Taxes 97  ')
        ->assertOk()
        ->assertJsonPath('data.label', 'Taxes 97');

    expect($disk->fresh()?->label)->toBe('Taxes 97');
});

it('wipes the label off a blank disk', function () {
    $player = User::factory()->create();
    $disk = PlayerFloppyDisk::factory()->blank()->create(['user_id' => $player->id, 'label' => 'Old']);

    labelDisk($player, $disk, ' ')->assertOk()->assertJsonPath('data.label', null);
});

it('limits the label length', function () {
    $player = User::factory()->create();
    $disk = PlayerFloppyDisk::factory()->blank()->create(['user_id' => $player->id]);

    labelDisk($player, $disk, str_repeat('a', LabelFloppyDiskRequest::LABEL_MAX_LENGTH + 1))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('label');
});

it('refuses to relabel printed disks and disks of other players', function () {
    $player = User::factory()->create();

    labelDisk($player, PlayerFloppyDisk::factory()->create(['user_id' => $player->id]), 'Mine now')->assertForbidden();
    labelDisk($player, PlayerFloppyDisk::factory()->blank()->create(), 'Mine now')->assertForbidden();
});
