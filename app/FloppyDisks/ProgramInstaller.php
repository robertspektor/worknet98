<?php

namespace App\FloppyDisks;

use App\Models\FloppyDisk;
use App\Models\InstalledProgram;
use App\Models\User;
use InvalidArgumentException;

class ProgramInstaller
{
    public function install(User $player, FloppyDisk $disk): InstalledProgram
    {
        if (! $disk->isInstallable()) {
            throw new InvalidArgumentException("Floppy disk [{$disk->slug}] has no program to install.");
        }

        return InstalledProgram::firstOrCreate(
            ['user_id' => $player->id, 'program' => $disk->program],
            ['floppy_disk_id' => $disk->id, 'installed_at' => now()],
        );
    }
}
