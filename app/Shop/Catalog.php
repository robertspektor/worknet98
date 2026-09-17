<?php

namespace App\Shop;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Catalog
{
    /**
     * @return list<ShopItem>
     */
    public function for(User $player, Storefront $storefront): array
    {
        $products = $storefront->productsForSale();
        $orders = Order::query()
            ->where('user_id', $player->id)
            ->where('product_type', $products->getModel()->getMorphClass())
            ->orderBy('id')
            ->get()
            ->keyBy('product_id');

        return array_values($products
            ->orderBy('price')
            ->orderBy('id')
            ->get()
            ->map(fn (Product&Model $product): ShopItem => new ShopItem($product, $orders->get($product->getKey())))
            ->all());
    }
}
