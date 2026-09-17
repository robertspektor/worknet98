<?php

use App\Models\DiskFile;
use App\Models\InstalledProgram;
use App\Models\PlayerFloppyDisk;
use App\Models\User;
use Illuminate\Testing\TestResponse;

function installFrom(User $player, DiskFile $file): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.disk-files.installation.store', $file));
}

function setupFileOf(User $player, string $program = 'minefield'): DiskFile
{
    return DiskFile::factory()->setup($program)->create([
        'player_floppy_disk_id' => PlayerFloppyDisk::factory()->create(['user_id' => $player->id]),
    ]);
}

it('installs the program from the setup file on a disk of the player', function () {
    $this->freezeSecond();
    $player = User::factory()->create();
    $setup = setupFileOf($player);

    installFrom($player, $setup)
        ->assertCreated()
        ->assertExactJson(['data' => 'minefield']);

    $installed = InstalledProgram::sole();
    expect($installed->user_id)->toBe($player->id)
        ->and($installed->floppy_disk_id)->toBe($setup->disk->floppy_disk_id)
        ->and($installed->installed_at->equalTo(now()))->toBeTrue();
});

it('keeps a program installed only once', function () {
    $player = User::factory()->create();
    $setup = setupFileOf($player);
    installFrom($player, $setup)->assertCreated();

    installFrom($player, $setup)->assertOk()->assertExactJson(['data' => 'minefield']);

    expect(InstalledProgram::count())->toBe(1);
});

it('refuses to install from a text file', function () {
    $player = User::factory()->create();
    $text = DiskFile::factory()->create(['player_floppy_disk_id' => PlayerFloppyDisk::factory()->create(['user_id' => $player->id])]);

    installFrom($player, $text)->assertForbidden();

    expect(InstalledProgram::count())->toBe(0);
});

it('refuses to install from a disk of another player', function () {
    installFrom(User::factory()->create(), setupFileOf(User::factory()->create()))->assertForbidden();

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
    $this->postJson(route('api.v1.disk-files.installation.store', setupFileOf(User::factory()->create())))
        ->assertUnauthorized();
});
