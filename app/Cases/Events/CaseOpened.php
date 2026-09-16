<?php

namespace App\Cases\Events;

use App\Models\WorkCase;
use Illuminate\Foundation\Events\Dispatchable;

class CaseOpened
{
    use Dispatchable;

    public function __construct(public readonly WorkCase $workCase) {}
}
