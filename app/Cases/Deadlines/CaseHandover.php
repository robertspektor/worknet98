<?php

namespace App\Cases\Deadlines;

use App\Cases\Routing\CaseRouter;
use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Models\Position;
use App\Models\WorkCase;

class CaseHandover
{
    public function __construct(
        private readonly CaseTemplateCatalog $templates,
        private readonly CaseRouter $router,
        private readonly ShipmentTemplateCatalog $shipmentTemplates,
    ) {}

    public function handOverToNpc(WorkCase $workCase): ?Position
    {
        $colleague = $this->colleagueFor($workCase);

        $colleague === null ? $this->lose($workCase) : $this->assign($workCase, $colleague);

        return $colleague;
    }

    private function colleagueFor(WorkCase $workCase): ?Position
    {
        $responsibility = match ($workCase->kind) {
            WorkCaseKind::Template => $this->templates->find($workCase->branch->company, $workCase->case_slug)?->responsibility,
            WorkCaseKind::Shipment => $this->shipmentTemplates->find($workCase->branch->company, $workCase->case_slug)?->responsibility,
            WorkCaseKind::Scripted => null,
        };

        return $responsibility === null ? null : $this->router->npcAssigneeFor($workCase->branch, $responsibility);
    }

    private function assign(WorkCase $workCase, Position $colleague): void
    {
        $workCase->update([
            'position_id' => $colleague->id,
            'employment_id' => null,
            'npc_due_at' => now()->addSeconds((int) config('game.npc_case_delay_seconds')),
        ]);
    }

    private function lose(WorkCase $workCase): void
    {
        $workCase->update(['status' => WorkCaseStatus::Lost, 'resolved_at' => now()]);
    }
}
