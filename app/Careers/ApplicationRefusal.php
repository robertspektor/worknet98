<?php

namespace App\Careers;

use App\Game\Refusal;

enum ApplicationRefusal: string implements Refusal
{
    case PositionClosed = 'position_closed';
    case LanguageMismatch = 'language_mismatch';
    case AlreadyEmployed = 'already_employed';
    case AlreadyApplied = 'already_applied';
    case ApplicationPending = 'application_pending';

    public function message(): string
    {
        return __('worknet.refusal.'.$this->value);
    }
}
