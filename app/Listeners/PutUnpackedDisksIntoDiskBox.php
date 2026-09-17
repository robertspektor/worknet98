<?php

namespace App\Listeners;

use App\FloppyDisks\DiskManufacturer;
use App\FloppyDisks\DiskSource;
use App\Models\FloppyDisk;
use App\Shop\Events\ParcelUnpacked;

class PutUnpackedDisksIntoDiskBox
{
    public function __construct(private readonly DiskManufacturer $manufacturer) {}

    public function handle(ParcelUnpacked $event): void
    {
        $product = $event->order->product;

        if (! $product instanceof FloppyDisk) {
            return;
        }

        for ($copy = 0; $copy < $product->pack_size; $copy++) {
            $this->manufacturer->make($event->order->user, $product, DiskSource::Shop, $event->order);
        }
    }
}
