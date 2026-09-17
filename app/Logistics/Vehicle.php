<?php

namespace App\Logistics;

enum Vehicle: string
{
    case Van = 'van';
    case Truck = 'truck';

    public function capacity(): int
    {
        return match ($this) {
            self::Van => 3,
            self::Truck => 2,
        };
    }

    public function carries(ShipmentSize $size): bool
    {
        return $this === self::Truck || $size === ShipmentSize::Parcel;
    }

    public function isCheapestFor(ShipmentSize $size): bool
    {
        return $this === match ($size) {
            ShipmentSize::Parcel => self::Van,
            ShipmentSize::Pallet => self::Truck,
        };
    }
}
