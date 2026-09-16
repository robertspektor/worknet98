<?php

namespace App\Careers\Events;

use App\Models\Employment;
use Illuminate\Foundation\Events\Dispatchable;

class PlayerHired
{
    use Dispatchable;

    public function __construct(public readonly Employment $employment) {}
}
