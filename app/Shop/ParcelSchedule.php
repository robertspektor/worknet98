<?php

namespace App\Shop;

use Carbon\CarbonImmutable;

class ParcelSchedule
{
    public function deliveryTime(): CarbonImmutable
    {
        $delay = config('game.parcel_delivery_delay_seconds');

        return $delay === null
            ? now()->addDay()->startOfDay()
            : now()->addSeconds((int) $delay);
    }
}
