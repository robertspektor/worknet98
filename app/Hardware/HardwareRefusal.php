<?php

namespace App\Hardware;

use App\Game\Refusal;

enum HardwareRefusal: string implements Refusal
{
    case ThermalPasteNotNeeded = 'thermal_paste_not_needed';

    public function message(): string
    {
        return __('hardware.refusal.'.$this->value);
    }
}
