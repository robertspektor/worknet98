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
use Illuminate\Support\Facades\DB;

class PartOrderer
{
    private const CARRIER_SERVICE = 'freight';

    private const SUPPLIER_SERVICE = 'plumbing-parts';

    public function __construct(
        private readonly CaseTemplateCatalog $caseTemplates,
        private readonly ShipmentTemplateCatalog $shipmentTemplates,
        private readonly ServiceProviders $providers,
        private readonly ShipmentCaseOpener $opener,
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
        $carrier = $this->providers->branchFor($city, self::CARRIER_SERVICE);
        $supplier = $this->providers->branchFor($city, self::SUPPLIER_SERVICE);
        $template = $carrier === null ? null : $this->shipmentTemplates->find($carrier->company, ShipmentTemplateCatalog::SPARE_PART_DELIVERY);

        if ($carrier === null || $supplier === null || $template === null) {
            return;
        }

        DB::transaction(function () use ($repairCase, $part, $appointment, $carrier, $supplier, $template): void {
            $shipment = Shipment::create([
                'branch_id' => $carrier->id,
                'sender_branch_id' => $supplier->id,
                'recipient_branch_id' => $repairCase->branch_id,
                'repair_case_id' => $repairCase->id,
                'contents' => $part->contents,
                'size' => $part->size,
                'due_date' => $appointment->date->toDateString(),
                'due_slot' => $appointment->slot,
            ]);

            $this->opener->open($shipment, $template);
        });
    }

    private function moveDueDate(Shipment $shipment, Appointment $appointment): void
    {
        if ($shipment->delivered_at === null) {
            $shipment->update(['due_date' => $appointment->date->toDateString(), 'due_slot' => $appointment->slot]);
        }
    }
}
