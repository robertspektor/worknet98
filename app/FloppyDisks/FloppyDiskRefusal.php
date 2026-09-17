<?php

namespace App\FloppyDisks;

use App\Game\Refusal;

enum FloppyDiskRefusal: string implements Refusal
{
    case WriteProtected = 'write_protected';
    case DiskFull = 'disk_full';

    public function message(): string
    {
        return __('floppy_disk.refusal.'.$this->value);
    }
}
