<?php

namespace App\Landing;

readonly class CityFigures
{
    public function __construct(
        public string $city,
        public int $residents,
        public int $companies,
        public int $openPositions,
        public int $deliveries,
    ) {}

    /**
     * @return array{residents: int, companies: int, open_positions: int, deliveries: int}
     */
    public function toArray(): array
    {
        return [
            'residents' => $this->residents,
            'companies' => $this->companies,
            'open_positions' => $this->openPositions,
            'deliveries' => $this->deliveries,
        ];
    }
}
