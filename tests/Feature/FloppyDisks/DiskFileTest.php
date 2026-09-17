<?php

use App\FloppyDisks\DiskFileKind;
use App\Models\DiskFile;
use App\Models\PlayerFloppyDisk;
use App\Models\User;
use Illuminate\Testing\TestResponse;

function saveOnDisk(User $player, PlayerFloppyDisk $disk, string $name, string $body): TestResponse
{
    return test()->actingAs($player)->postJson(route('api.v1.floppy-disks.files.store', $disk), ['name' => $name, 'body' => $body]);
}

function blankDiskOf(User $player): PlayerFloppyDisk
{
    return PlayerFloppyDisk::factory()->blank()->create(['user_id' => $player->id]);
}

it('lists the files on a disk with their text in the player language', function () {
    $player = User::factory()->create(['locale' => 'de']);
    $disk = PlayerFloppyDisk::factory()->create(['user_id' => $player->id]);
    $readme = DiskFile::factory()->create(['player_floppy_disk_id' => $disk->id, 'name' => 'README.TXT', 'body' => null, 'content_key' => 'floppy_drive.empty', 'size_bytes' => 19]);
    $setup = DiskFile::factory()->setup('minefield', 318_464)->create(['player_floppy_disk_id' => $disk->id]);
    DiskFile::factory()->create();

    $this->actingAs($player)
        ->getJson(route('api.v1.floppy-disks.files.index', $disk))
        ->assertOk()
        ->assertExactJson(['data' => [
            ['id' => $readme->id, 'name' => 'README.TXT', 'kind' => 'text', 'size_bytes' => 19, 'program' => null, 'text' => 'Diese Diskette ist leer.'],
            ['id' => $setup->id, 'name' => 'SETUP.EXE', 'kind' => 'setup', 'size_bytes' => 318_464, 'program' => 'minefield', 'text' => null],
        ]]);
});

it('saves a text file on a writable disk', function () {
    $player = User::factory()->create();
    $disk = blankDiskOf($player);

    saveOnDisk($player, $disk, 'letter.txt', "Dear Sir,\n")
        ->assertCreated()
        ->assertJsonPath('data.name', 'LETTER.TXT')
        ->assertJsonPath('data.text', "Dear Sir,\n")
        ->assertJsonPath('data.size_bytes', 10);

    $file = DiskFile::sole();
    expect($file->player_floppy_disk_id)->toBe($disk->id)
        ->and($file->kind)->toBe(DiskFileKind::Text);
});

it('replaces a file with the same name', function () {
    $player = User::factory()->create();
    $disk = blankDiskOf($player);
    saveOnDisk($player, $disk, 'NOTE.TXT', 'First draft')->assertCreated();

    saveOnDisk($player, $disk, 'NOTE.TXT', 'Final')->assertOk()->assertJsonPath('data.size_bytes', 5);

    expect(DiskFile::sole()->body)->toBe('Final');
});

it('refuses to write on a write-protected disk', function () {
    $player = User::factory()->create();
    $disk = PlayerFloppyDisk::factory()->writeProtected()->create(['user_id' => $player->id]);

    saveOnDisk($player, $disk, 'NOTE.TXT', 'Hello')
        ->assertUnprocessable()
        ->assertExactJson(['message' => 'Cannot write to A:\\. The disk is write-protected.', 'refusal' => 'write_protected']);

    expect(DiskFile::count())->toBe(0);
});

it('refuses a file that does not fit on the disk', function () {
    $player = User::factory()->create();
    $disk = blankDiskOf($player);
    DiskFile::factory()->setup('minefield', PlayerFloppyDisk::CAPACITY_BYTES - 4)->create(['player_floppy_disk_id' => $disk->id]);

    saveOnDisk($player, $disk, 'NOTE.TXT', 'Hello')->assertUnprocessable()->assertJsonPath('refusal', 'disk_full');
    saveOnDisk($player, $disk, 'NOTE.TXT', 'Hey!')->assertCreated();
});

it('counts the replaced file as free space', function () {
    $player = User::factory()->create();
    $disk = blankDiskOf($player);
    DiskFile::factory()->setup('minefield', PlayerFloppyDisk::CAPACITY_BYTES - 10)->create(['player_floppy_disk_id' => $disk->id]);
    saveOnDisk($player, $disk, 'NOTE.TXT', '0123456789')->assertCreated();

    saveOnDisk($player, $disk, 'NOTE.TXT', '9876543210')->assertOk();
});

it('only accepts short file names with a text extension', function (string $name) {
    $player = User::factory()->create();

    saveOnDisk($player, blankDiskOf($player), $name, 'Hello')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
})->with(['TOOLONGNAME.TXT', 'NOTE.EXE', 'NO SPACE.TXT', '.TXT', '']);

it('refuses to write on the disk of another player', function () {
    saveOnDisk(User::factory()->create(), blankDiskOf(User::factory()->create()), 'NOTE.TXT', 'Hello')->assertForbidden();
    test()->actingAs(User::factory()->create())
        ->getJson(route('api.v1.floppy-disks.files.index', blankDiskOf(User::factory()->create())))
        ->assertForbidden();
});

it('deletes a file from a writable disk', function () {
    $player = User::factory()->create();
    $file = DiskFile::factory()->create(['player_floppy_disk_id' => blankDiskOf($player)]);

    $this->actingAs($player)->deleteJson(route('api.v1.disk-files.destroy', $file))->assertNoContent();

    expect(DiskFile::count())->toBe(0);
});

it('refuses to delete files from a write-protected disk', function () {
    $player = User::factory()->create();
    $file = DiskFile::factory()->create(['player_floppy_disk_id' => PlayerFloppyDisk::factory()->writeProtected()->create(['user_id' => $player->id])]);

    $this->actingAs($player)->deleteJson(route('api.v1.disk-files.destroy', $file))->assertJsonPath('refusal', 'write_protected');
    $this->actingAs(User::factory()->create())->deleteJson(route('api.v1.disk-files.destroy', $file))->assertForbidden();

    expect(DiskFile::count())->toBe(1);
});
