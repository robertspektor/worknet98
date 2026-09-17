<?php

namespace App\Hardware;

use App\Models\HardwarePart;

readonly class InstalledCpu
{
    public function __construct(
        public HardwarePart $part,
        public bool $needsThermalPaste,
    ) {}
}
