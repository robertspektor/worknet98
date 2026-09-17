<?php

namespace App\Shop;

use App\Models\Order;

enum ShopItemStatus: string
{
    case Available = 'available';
    case Ordered = 'ordered';
    case Delivered = 'delivered';
    case Owned = 'owned';

    public static function of(?Order $order, bool $canBeOrderedRepeatedly): self
    {
        return match (true) {
            $order === null => self::Available,
            $canBeOrderedRepeatedly && $order->isUnpacked() => self::Available,
            $order->isUnpacked() => self::Owned,
            $order->isDelivered() => self::Delivered,
            default => self::Ordered,
        };
    }
}
