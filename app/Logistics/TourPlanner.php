<?php

namespace App\Logistics;

use App\Game\ActionRefused;
use App\Models\Driver;
use App\Models\Employment;
use App\Models\Position;
use App\Models\Shipment;
use App\Models\User;
use App\Work\DutyCheck;
use App\Workplace\BookingWindow;
use Illuminate\Support\Facades\DB;

class TourPlanner
{
    public function __construct(
        private readonly DutyCheck $dutyCheck,
        private readonly BookingWindow $window,
    ) {}

    public function plan(User $player, ShipmentPlan $plan): Shipment
    {
        $employment = $this->dutyCheck->employmentOnDuty($player);

        return $this->record($plan, $employment->position, $employment);
    }

    public function planForNpc(ShipmentPlan $plan, Position $position): Shipment
    {
        return $this->record($plan, $position, null);
    }

    public function unplan(User $player, Shipment $shipment): void
    {
        $this->dutyCheck->employmentOnDuty($player);

        if ($shipment->delivered_at !== null) {
            throw new ActionRefused(TourRefusal::AlreadyDelivered);
        }

        $shipment->update(['driver_id' => null, 'tour_date' => null, 'tour' => null, 'planned_by_employment_id' => null, 'planned_by_position_id' => null]);
    }

    private function record(ShipmentPlan $plan, Position $position, ?Employment $employment): Shipment
    {
        return DB::transaction(function () use ($plan, $position, $employment): Shipment {
            Driver::query()->whereKey($plan->driver->id)->lockForUpdate()->first();
            $this->ensurePlannable($plan);

            $plan->shipment->update([
                'driver_id' => $plan->driver->id,
                'tour_date' => $plan->date->toDateString(),
                'tour' => $plan->tour,
                'planned_by_employment_id' => $employment?->id,
                'planned_by_position_id' => $position->id,
            ]);

            return $plan->shipment;
        });
    }

    private function ensurePlannable(ShipmentPlan $plan): void
    {
        $refusal = match (true) {
            $plan->shipment->delivered_at !== null => TourRefusal::AlreadyDelivered,
            ! $this->window->contains($plan->date) => TourRefusal::OutsideBookingWindow,
            ! $plan->driver->vehicle->carries($plan->shipment->size) => TourRefusal::VehicleTooSmall,
            $plan->driver->isBusyOn($plan->date, $plan->tour) => TourRefusal::DriverBusy,
            $this->isFull($plan) => TourRefusal::TourFull,
            default => null,
        };

        if ($refusal !== null) {
            throw new ActionRefused($refusal);
        }
    }

    private function isFull(ShipmentPlan $plan): bool
    {
        $load = $plan->driver->shipments()
            ->whereDate('tour_date', $plan->date->toDateString())
            ->where('tour', $plan->tour)
            ->whereKeyNot($plan->shipment->id)
            ->count();

        return $load >= $plan->driver->vehicle->capacity();
    }
}
