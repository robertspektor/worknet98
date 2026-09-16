<?php

use App\Models\FloppyDisk;
use App\Models\InstalledProgram;
use App\Models\User;
use Illuminate\Testing\TestResponse;

function installFrom(User $player, FloppyDisk $disk): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.floppy-disks.installation.store', $disk));
}

it('installs the program from a disk in the disk box', function () {
    $this->freezeSecond();
    $player = User::factory()->create();
    $disk = FloppyDisk::factory()->starter()->program('minefield')->create();

    installFrom($player, $disk)
        ->assertCreated()
        ->assertExactJson(['data' => 'minefield']);

    $installed = InstalledProgram::sole();
    expect($installed->user_id)->toBe($player->id)
        ->and($installed->floppy_disk_id)->toBe($disk->id)
        ->and($installed->installed_at->equalTo(now()))->toBeTrue();
});

it('keeps a program installed only once', function () {
    $player = User::factory()->create();
    $disk = FloppyDisk::factory()->starter()->program('minefield')->create();
    installFrom($player, $disk)->assertCreated();

    installFrom($player, $disk)->assertOk()->assertExactJson(['data' => 'minefield']);

    expect(InstalledProgram::count())->toBe(1);
});

it('refuses to install from a disk without a program', function () {
    installFrom(User::factory()->create(), FloppyDisk::factory()->starter()->create())->assertForbidden();

    expect(InstalledProgram::count())->toBe(0);
});

it('refuses to install from a disk that is not in the disk box', function () {
    installFrom(User::factory()->create(), FloppyDisk::factory()->program('minefield')->create())->assertForbidden();

    expect(InstalledProgram::count())->toBe(0);
});

it('lists the installed programs of the player in installation order', function () {
    $player = User::factory()->create();
    InstalledProgram::factory()->create(['user_id' => $player->id, 'program' => 'minefield', 'installed_at' => now()->subDay()]);
    InstalledProgram::factory()->create(['user_id' => $player->id, 'program' => 'paint', 'installed_at' => now()]);
    InstalledProgram::factory()->create();

    $this->actingAs($player)
        ->getJson(route('api.v1.installed-programs.index'))
        ->assertOk()
        ->assertExactJson(['data' => ['minefield', 'paint']]);
});

it('requires a signed-in player to install programs', function () {
    $this->postJson(route('api.v1.floppy-disks.installation.store', FloppyDisk::factory()->starter()->program('minefield')->create()))
        ->assertUnauthorized();
});
