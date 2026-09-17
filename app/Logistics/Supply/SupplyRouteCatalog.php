<?php

namespace App\Logistics\Supply;

use App\Logistics\ShipmentSize;
use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Models\City;
use Illuminate\Support\Facades\File;

class SupplyRouteCatalog
{
    private const DEFAULT_DUE_TIME = '12:00';

    /**
     * @return list<SupplyRoute>
     */
    public function routesIn(City $city): array
    {
        $path = database_path("content/supply_routes/{$city->slug}.json");

        if (! File::exists($path)) {
            return [];
        }

        /** @var list<array{slug: string, supplier: string, recipient: string, contents: string, size: string, daily_rate: float|int, lead_work_days: int, template?: string, due_time?: string}> $entries */
        $entries = File::json($path, JSON_THROW_ON_ERROR);

        return array_map(fn (array $entry): SupplyRoute => new SupplyRoute(
            slug: $entry['slug'],
            supplier: $entry['supplier'],
            recipient: $entry['recipient'],
            contents: $entry['contents'],
            size: ShipmentSize::from($entry['size']),
            dailyRate: (float) $entry['daily_rate'],
            leadWorkDays: $entry['lead_work_days'],
            templateSlug: $entry['template'] ?? ShipmentTemplateCatalog::RESTOCK_DELIVERY,
            dueTime: $entry['due_time'] ?? self::DEFAULT_DUE_TIME,
        ), $entries);
    }
}
