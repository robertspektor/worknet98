<?php

namespace App\Shop;

use App\Models\FloppyDisk;
use App\Models\HardwarePart;
use Illuminate\Database\Eloquent\Builder;

enum Storefront: string
{
    case DiskDepot = 'diskdepot';
    case ChipCity = 'chipcity';

    public function senderName(): string
    {
        return match ($this) {
            self::DiskDepot => 'DiskDepot',
            self::ChipCity => 'ChipCity',
        };
    }

    public function senderAddress(): string
    {
        return "orders@{$this->value}.wn";
    }

    /**
     * @return Builder<FloppyDisk>|Builder<HardwarePart>
     */
    public function productsForSale(): Builder
    {
        return match ($this) {
            self::DiskDepot => FloppyDisk::query()->forSale(),
            self::ChipCity => HardwarePart::query()->forSale(),
        };
    }
}
