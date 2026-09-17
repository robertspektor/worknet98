<?php

use App\FloppyDisks\DiskFileKind;
use App\FloppyDisks\FloppyDiskKind;
use App\Models\FloppyDisk;
use Database\Seeders\FloppyDiskSeeder;

it('seeds starter disks including an installable program', function () {
    $this->seed(FloppyDiskSeeder::class);

    $starters = FloppyDisk::query()->starter()->get();

    expect($starters)->not->toBeEmpty()
        ->and($starters->contains(fn (FloppyDisk $disk): bool => collect($disk->catalogFiles())
            ->contains(fn ($file): bool => $file->kind === DiskFileKind::Setup && $file->program !== null)))->toBeTrue();
});

it('seeds blank disks sold in packs', function () {
    $this->seed(FloppyDiskSeeder::class);

    $blankDisks = FloppyDisk::query()->where('kind', FloppyDiskKind::Blank)->sole();

    expect($blankDisks->isForSale())->toBeTrue()
        ->and($blankDisks->pack_size)->toBeGreaterThan(1)
        ->and($blankDisks->catalogFiles())->toBe([]);
});

it('gives every text file on a seeded disk a translation in each language', function () {
    $this->seed(FloppyDiskSeeder::class);

    $contentKeys = FloppyDisk::all()
        ->flatMap(fn (FloppyDisk $disk): array => $disk->catalogFiles())
        ->pluck('contentKey')
        ->filter();

    foreach (['en', 'de'] as $locale) {
        expect($contentKeys->reject(fn (string $key): bool => __($key, [], $locale) !== $key)->all())->toBe([]);
    }
});

it('can seed the floppy disks repeatedly without duplicates', function () {
    $this->seed(FloppyDiskSeeder::class);
    $count = FloppyDisk::count();

    $this->seed(FloppyDiskSeeder::class);

    expect(FloppyDisk::count())->toBe($count);
});
