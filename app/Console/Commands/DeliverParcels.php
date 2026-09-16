<?php

namespace App\Console\Commands;

use App\Shop\ParcelCourier;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('shop:deliver-parcels')]
#[Description('Deliver ordered floppy disks whose delivery time has come')]
class DeliverParcels extends Command
{
    public function handle(ParcelCourier $courier): int
    {
        $this->info("Delivered {$courier->deliverDue()} parcel(s).");

        return self::SUCCESS;
    }
}
