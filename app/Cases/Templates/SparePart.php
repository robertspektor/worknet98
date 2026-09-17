<?php

namespace App\Cases\Templates;

use App\Logistics\ShipmentSize;

readonly class SparePart
{
    /**
     * @param  array{subject: string, body: string}  $missingMail
     */
    public function __construct(
        public string $contents,
        public ShipmentSize $size,
        public array $missingMail,
    ) {}
}
