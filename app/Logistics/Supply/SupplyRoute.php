<?php

namespace App\Logistics\Supply;

use App\Logistics\ShipmentSize;

readonly class SupplyRoute
{
    public function __construct(
        public string $slug,
        public string $supplier,
        public string $recipient,
        public string $contents,
        public ShipmentSize $size,
        public float $dailyRate,
        public int $leadWorkDays,
    ) {}
}
