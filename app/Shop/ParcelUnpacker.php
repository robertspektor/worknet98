<?php

namespace App\Shop;

use App\Models\Order;
use App\Shop\Events\ParcelUnpacked;
use Illuminate\Support\Facades\DB;

class ParcelUnpacker
{
    public function unpack(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $parcel = Order::query()->whereKey($order->id)->whereNull('unpacked_at')->lockForUpdate()->first();

            if ($parcel === null) {
                return;
            }

            $parcel->update(['unpacked_at' => now()]);
            ParcelUnpacked::dispatch($parcel);
        });
    }
}
