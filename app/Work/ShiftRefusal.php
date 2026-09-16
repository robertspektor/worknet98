<?php

namespace App\Work;

use App\Game\Refusal;

enum ShiftRefusal: string implements Refusal
{
    case NotEmployed = 'not_employed';
    case AlreadyOnDuty = 'already_on_duty';
    case ShiftDone = 'shift_done';
    case NotOnDuty = 'not_on_duty';

    public function message(): string
    {
        return __('time_clock.refusal.'.$this->value);
    }
}
