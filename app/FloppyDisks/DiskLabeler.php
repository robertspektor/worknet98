<?php

namespace App\FloppyDisks;

use App\Models\PlayerFloppyDisk;

class DiskLabeler
{
    public function label(PlayerFloppyDisk $disk, ?string $label): void
    {
        $disk->update(['label' => $label]);
    }
}
