<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class ApplicationDecided implements Condition
{
    public function isMetBy(CaseState $state): bool
    {
        return $state->civilApplication()?->isDecided() ?? false;
    }
}
