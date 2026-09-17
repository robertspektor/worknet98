<?php

namespace App\Career\Events;

use App\Models\Employment;
use App\Models\Position;
use Illuminate\Foundation\Events\Dispatchable;

class PlayerPromoted
{
    use Dispatchable;

    public function __construct(
        public readonly Employment $employment,
        public readonly Position $previousPosition,
    ) {}
}
