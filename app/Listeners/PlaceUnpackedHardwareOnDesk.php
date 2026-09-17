<?php

namespace App\Listeners;

use App\Hardware\DeskParts;
use App\Models\HardwarePart;
use App\Shop\Events\ParcelUnpacked;

class PlaceUnpackedHardwareOnDesk
{
    public function __construct(private readonly DeskParts $deskParts) {}

    public function handle(ParcelUnpacked $event): void
    {
        $product = $event->order->product;

        if ($product instanceof HardwarePart) {
            $this->deskParts->place($event->order->user, $product);
        }
    }
}
