<?php

namespace App\Logistics;

use App\Cases\Templates\CaseTemplate;
use App\Models\WorkCase;
use App\Workplace\BookingWindow;
use Carbon\CarbonImmutable;

class PartLeadTime
{
    public function __construct(private readonly BookingWindow $window) {}

    public function earliestRepairStart(WorkCase $repairCase, CaseTemplate $template): ?CarbonImmutable
    {
        $shipment = $repairCase->partShipment;

        return match (true) {
            $template->part === null, $shipment?->delivered_at !== null => null,
            $shipment?->isPlanned() === true => $shipment->plannedArrival(),
            default => Tour::Afternoon->endsAt($this->window->days()[0]),
        };
    }
}
