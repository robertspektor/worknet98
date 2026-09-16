<?php

namespace App\Work\Events;

use App\Models\Shift;
use Illuminate\Foundation\Events\Dispatchable;

class ShiftStarted
{
    use Dispatchable;

    public function __construct(public readonly Shift $shift) {}
}
