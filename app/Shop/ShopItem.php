<?php

namespace App\Shop;

use App\Models\FloppyDisk;
use App\Models\FloppyDiskOrder;

readonly class ShopItem
{
    public function __construct(
        public FloppyDisk $disk,
        public ?FloppyDiskOrder $order,
    ) {}

    public function status(): ShopItemStatus
    {
        return ShopItemStatus::of($this->order);
    }
}
