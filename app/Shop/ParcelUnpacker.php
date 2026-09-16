<?php

namespace App\Shop;

use App\Models\FloppyDiskOrder;

class ParcelUnpacker
{
    public function unpack(FloppyDiskOrder $order): void
    {
        if (! $order->isUnpacked()) {
            $order->update(['unpacked_at' => now()]);
        }
    }
}
