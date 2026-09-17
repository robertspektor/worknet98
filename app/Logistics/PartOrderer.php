<?php

namespace App\Logistics;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\SparePart;
use App\Cases\WorkCaseKind;
use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Models\Appointment;
use App\Models\Shipment;
use App\Models\WorkCase;
use App\World\Events\ServiceProviders;

class PartOrderer
{
    private const SUPPLIER_SERVICE = 'plumbing-parts';

    public function __construct(
        private readonly CaseTemplateCatalog $caseTemplates,
        private readonly ServiceProviders $providers,
        private readonly ShipmentDispatch $dispatch,
    ) {}

    public function orderFor(Appointment $appointment): void
    {
        $repairCase = WorkCase::query()
            ->where('customer_id', $appointment->customer_id)
            ->where('kind', WorkCaseKind::Template)
            ->open()
            ->oldest('id')
            ->with(['branch.company', 'branch.city', 'partShipment'])
            ->first();
        $part = $repairCase === null ? null : $this->caseTemplates->find($repairCase->branch->company, $repairCase->case_slug)?->part;

        if ($repairCase === null || $part === null) {
            return;
        }

        $shipment = $repairCase->partShipment;

        $shipment === null
            ? $this->order($repairCase, $part, $appointment)
            : $this->moveDueDate($shipment, $appointment);
    }

    private function order(WorkCase $repairCase, SparePart $part, Appointment $appointment): void
    {
        $city = $repairCase->branch->city;
        $supplier = $this->providers->branchFor($city, self::SUPPLIER_SERVICE);

        if ($supplier === null) {
            return;
        }

        $this->dispatch->send($city, $supplier, $repairCase->branch, ShipmentTemplateCatalog::SPARE_PART_DELIVERY, [
            'repair_case_id' => $repairCase->id,
            'contents' => $part->contents,
            'size' => $part->size,
            'due_date' => $appointment->date->toDateString(),
            'due_slot' => $appointment->slot,
        ]);
    }

    private function moveDueDate(Shipment $shipment, Appointment $appointment): void
    {
        if ($shipment->delivered_at === null) {
            $shipment->update(['due_date' => $appointment->date->toDateString(), 'due_slot' => $appointment->slot]);
        }
    }
}
