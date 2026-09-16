<?php

namespace App\Shop;

use App\Models\FloppyDisk;
use App\Models\FloppyDiskOrder;
use App\Models\User;

class DiskShop
{
    /**
     * @return list<ShopItem>
     */
    public function catalogFor(User $player): array
    {
        $orders = FloppyDiskOrder::query()->where('user_id', $player->id)->get()->keyBy('floppy_disk_id');

        return array_values(FloppyDisk::query()
            ->forSale()
            ->orderBy('price')
            ->orderBy('id')
            ->get()
            ->map(fn (FloppyDisk $disk): ShopItem => new ShopItem($disk, $orders->get($disk->id)))
            ->all());
    }
}
