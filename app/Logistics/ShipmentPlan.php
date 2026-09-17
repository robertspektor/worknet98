<?php

namespace App\Logistics;

use App\Models\Driver;
use App\Models\Shipment;
use Carbon\CarbonImmutable;

readonly class ShipmentPlan
{
    public function __construct(
        public Shipment $shipment,
        public Driver $driver,
        public CarbonImmutable $date,
        public Tour $tour,
    ) {}

    public function arrival(): CarbonImmutable
    {
        return $this->tour->endsAt($this->date);
    }
}
