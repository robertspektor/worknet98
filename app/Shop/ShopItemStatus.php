<?php

namespace App\Shop;

use App\Models\FloppyDiskOrder;

enum ShopItemStatus: string
{
    case Available = 'available';
    case Ordered = 'ordered';
    case Delivered = 'delivered';
    case Owned = 'owned';

    public static function of(?FloppyDiskOrder $order): self
    {
        return match (true) {
            $order === null => self::Available,
            $order->isUnpacked() => self::Owned,
            $order->isDelivered() => self::Delivered,
            default => self::Ordered,
        };
    }
}
