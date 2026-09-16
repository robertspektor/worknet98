<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

interface Condition
{
    public function isMetBy(CaseState $state): bool;
}
