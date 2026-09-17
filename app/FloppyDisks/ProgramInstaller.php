<?php

namespace App\FloppyDisks;

use App\Models\DiskFile;
use App\Models\InstalledProgram;
use App\Models\User;
use InvalidArgumentException;

class ProgramInstaller
{
    public function install(User $player, DiskFile $setupFile): InstalledProgram
    {
        if (! $setupFile->isInstallable()) {
            throw new InvalidArgumentException("Disk file [{$setupFile->id}] has no program to install.");
        }

        return InstalledProgram::firstOrCreate(
            ['user_id' => $player->id, 'program' => $setupFile->program],
            ['floppy_disk_id' => $setupFile->disk->floppy_disk_id, 'installed_at' => now()],
        );
    }
}
