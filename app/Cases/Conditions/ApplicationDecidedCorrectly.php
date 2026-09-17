<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class ApplicationDecidedCorrectly implements Condition
{
    public function isMetBy(CaseState $state): bool
    {
        return $state->civilApplication()?->isDecidedCorrectly() ?? false;
    }
}
