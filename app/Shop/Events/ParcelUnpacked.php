<?php

namespace App\Shop\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;

class ParcelUnpacked
{
    use Dispatchable;

    public function __construct(public readonly Order $order) {}
}
