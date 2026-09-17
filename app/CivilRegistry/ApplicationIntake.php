<?php

namespace App\CivilRegistry;

use App\CivilRegistry\Templates\ApplicationTemplateCatalog;
use App\Models\Branch;
use App\Models\Position;
use App\Models\WorkCase;
use App\Models\WorldEvent;

class ApplicationIntake
{
    public function __construct(
        private readonly ApplicationTemplateCatalog $templates,
        private readonly ApplicationFactory $factory,
        private readonly ApplicationCaseOpener $opener,
    ) {}

    public function receive(Branch $office, WorldEvent $event, ApplicationKind $kind, ?Position $assignee = null): ?WorkCase
    {
        $template = $this->templates->forKind($office->company, $kind);
        $application = $template === null ? null : $this->factory->create($office, $event, $kind);

        return $template === null || $application === null ? null : $this->opener->open($application, $template, $event, $assignee);
    }
}
