<?php

namespace App\Cases;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateCaseBuilder;
use App\Logistics\Templates\ShipmentCaseBuilder;
use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Models\WorkCase;

class CaseDefinitions
{
    public function __construct(
        private readonly CaseCatalog $scripted,
        private readonly CaseTemplateCatalog $templates,
        private readonly TemplateCaseBuilder $builder,
        private readonly ShipmentTemplateCatalog $shipmentTemplates,
        private readonly ShipmentCaseBuilder $shipmentBuilder,
    ) {}

    public function for(WorkCase $workCase): ?CaseDefinition
    {
        $company = $workCase->branch->company;

        if ($workCase->kind === WorkCaseKind::Scripted) {
            return $this->scripted->find($company, $workCase->case_slug);
        }

        if ($workCase->kind === WorkCaseKind::Shipment) {
            return $this->forShipment($workCase);
        }

        $template = $this->templates->find($company, $workCase->case_slug);

        return $template === null ? null : $this->builder->build($template, $workCase->customer);
    }

    private function forShipment(WorkCase $workCase): ?CaseDefinition
    {
        $template = $this->shipmentTemplates->find($workCase->branch->company, $workCase->case_slug);
        $shipment = $workCase->shipment;

        return $template === null || $shipment === null ? null : $this->shipmentBuilder->build($template, $shipment, $workCase->customer);
    }
}
