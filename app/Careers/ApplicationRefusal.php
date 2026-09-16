<?php

namespace App\Careers;

enum ApplicationRefusal: string
{
    case LanguageMismatch = 'language_mismatch';
    case AlreadyEmployed = 'already_employed';
    case AlreadyApplied = 'already_applied';
    case ApplicationPending = 'application_pending';

    public function message(): string
    {
        return __('worknet.refusal.'.$this->value);
    }
}
