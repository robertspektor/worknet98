<?php

namespace App\Logistics\Supply;

use App\Game\GameClock;
use App\Logistics\ReachableDue;
use App\Logistics\ShipmentDispatch;
use App\Models\City;
use App\Models\Shipment;
use App\World\Events\ServiceProviders;
use Illuminate\Database\UniqueConstraintViolationException;

class SupplyOrderGenerator
{
    public function __construct(
        private readonly GameClock $clock,
        private readonly SupplyRouteCatalog $routes,
        private readonly SupplyOrderPlanner $planner,
        private readonly ServiceProviders $providers,
        private readonly ShipmentDispatch $dispatch,
        private readonly ReachableDue $due,
    ) {}

    public function generateDue(): int
    {
        return City::query()->orderBy('id')->get()->sum($this->generateFor(...));
    }

    private function generateFor(City $city): int
    {
        $now = $this->clock->now();
        $due = array_filter(
            $this->planner->plan($city, $this->routes->routesIn($city), $now->startOfDay()),
            fn (PlannedSupplyOrder $order): bool => $order->placedAt->lte($now) && ! Shipment::query()->where('order_key', $order->key)->exists(),
        );

        return count(array_filter($due, fn (PlannedSupplyOrder $order): bool => $this->place($city, $order)));
    }

    private function place(City $city, PlannedSupplyOrder $order): bool
    {
        $supplier = $this->providers->branchOf($city, $order->route->supplier);
        $recipient = $this->providers->branchOf($city, $order->route->recipient);

        if ($supplier === null || $recipient === null) {
            return false;
        }

        $due = $this->due->forCarrier($this->providers->branchFor($city, ShipmentDispatch::CARRIER_SERVICE), $order->route->size, $order->dueAt());

        try {
            return $this->dispatch->send($city, $supplier, $recipient, $order->route->templateSlug, [
                'order_key' => $order->key,
                'contents' => $order->route->contents,
                'size' => $order->route->size,
                'due_date' => $due->toDateString(),
                'due_slot' => $due->format('H:i'),
            ]) !== null;
        } catch (UniqueConstraintViolationException) {
            return false;
        }
    }
}
