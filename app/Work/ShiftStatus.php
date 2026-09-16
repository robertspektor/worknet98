<?php

namespace App\Work;

use App\Models\Employment;
use App\Models\Shift;

readonly class ShiftStatus
{
    public function __construct(
        public Employment $employment,
        public ?Shift $shift,
    ) {}

    public function state(): string
    {
        return match (true) {
            $this->shift === null => 'off_duty',
            $this->shift->isOnDuty() => 'on_duty',
            default => 'done',
        };
    }
}
