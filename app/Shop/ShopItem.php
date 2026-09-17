<?php

namespace App\Shop;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

readonly class ShopItem
{
    public function __construct(
        public Product&Model $product,
        public ?Order $order,
    ) {}

    public function status(): ShopItemStatus
    {
        return ShopItemStatus::of($this->order);
    }
}
