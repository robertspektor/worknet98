<?php

namespace App\Cases;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateCaseBuilder;
use App\CivilRegistry\Templates\ApplicationCaseBuilder;
use App\CivilRegistry\Templates\ApplicationTemplateCatalog;
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
        private readonly ApplicationTemplateCatalog $applicationTemplates,
        private readonly ApplicationCaseBuilder $applicationBuilder,
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

        if ($workCase->kind === WorkCaseKind::Application) {
            return $this->forApplication($workCase);
        }

        $template = $this->templates->find($company, $workCase->case_slug);

        return $template === null ? null : $this->builder->build($template, $workCase->customer);
    }

    private function forApplication(WorkCase $workCase): ?CaseDefinition
    {
        $template = $this->applicationTemplates->find($workCase->branch->company, $workCase->case_slug);
        $application = $workCase->civilApplication;

        return $template === null || $application === null ? null : $this->applicationBuilder->build($template, $application);
    }

    private function forShipment(WorkCase $workCase): ?CaseDefinition
    {
        $template = $this->shipmentTemplates->find($workCase->branch->company, $workCase->case_slug);
        $shipment = $workCase->shipment;

        return $template === null || $shipment === null ? null : $this->shipmentBuilder->build($template, $shipment, $workCase->customer);
    }
}
