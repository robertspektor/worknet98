<?php

use App\Models\FloppyDisk;
use Database\Seeders\FloppyDiskSeeder;

it('seeds starter disks including an installable program', function () {
    $this->seed(FloppyDiskSeeder::class);

    $starters = FloppyDisk::query()->starter()->get();

    expect($starters)->not->toBeEmpty()
        ->and($starters->contains(fn (FloppyDisk $disk): bool => $disk->isInstallable()))->toBeTrue();
});

it('can seed the floppy disks repeatedly without duplicates', function () {
    $this->seed(FloppyDiskSeeder::class);
    $count = FloppyDisk::count();

    $this->seed(FloppyDiskSeeder::class);

    expect(FloppyDisk::count())->toBe($count);
});
