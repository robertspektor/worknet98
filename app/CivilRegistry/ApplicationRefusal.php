<?php

namespace App\CivilRegistry;

use App\Game\Refusal;

enum ApplicationRefusal: string implements Refusal
{
    case AlreadyDecided = 'already_decided';

    public function message(): string
    {
        return __('registry.refusal.'.$this->value);
    }
}
