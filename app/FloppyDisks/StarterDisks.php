<?php

namespace App\FloppyDisks;

use App\Models\FloppyDisk;
use App\Models\PlayerFloppyDisk;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;

class StarterDisks
{
    public function __construct(private readonly DiskManufacturer $manufacturer) {}

    public function handOutTo(User $player): void
    {
        $handedOut = PlayerFloppyDisk::query()
            ->where('user_id', $player->id)
            ->where('source', DiskSource::Starter)
            ->pluck('floppy_disk_id');

        FloppyDisk::query()
            ->starter()
            ->whereNotIn('id', $handedOut)
            ->orderBy('id')
            ->each(fn (FloppyDisk $disk) => $this->handOut($player, $disk));
    }

    private function handOut(User $player, FloppyDisk $disk): void
    {
        try {
            $this->manufacturer->make($player, $disk, DiskSource::Starter);
        } catch (UniqueConstraintViolationException) {
            return;
        }
    }
}
