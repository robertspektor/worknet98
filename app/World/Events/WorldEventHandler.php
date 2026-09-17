<?php

namespace App\World\Events;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateCaseOpener;
use App\CivilRegistry\ApplicationIntake;
use App\CivilRegistry\ApplicationKind;
use App\Models\WorkCase;
use App\Models\WorldEvent;
use App\Workplace\CustomerIntake;
use LogicException;

class WorldEventHandler
{
    public function __construct(
        private readonly EventCatalog $catalog,
        private readonly ServiceProviders $providers,
        private readonly CaseTemplateCatalog $templates,
        private readonly CustomerIntake $intake,
        private readonly TemplateCaseOpener $opener,
        private readonly ApplicationIntake $applications,
    ) {}

    public function handle(WorldEvent $event): ?WorkCase
    {
        $definition = $this->catalog->find($event->type);

        $branch = $definition->service === null ? null : $this->providers->branchFor($event->city, $definition->service);

        if ($branch === null) {
            return null;
        }

        if ($definition->application !== null) {
            return $this->applications->receive($branch, $event, ApplicationKind::from($definition->application));
        }

        if ($definition->caseTemplate === null) {
            return null;
        }

        $template = $this->templates->find($branch->company, $definition->caseTemplate)
            ?? throw new LogicException("Company [{$branch->company->slug}] has no case template [{$definition->caseTemplate}].");

        return $this->opener->open($branch, $template, $this->intake->customerFor($branch, $event->person), $event);
    }
}
