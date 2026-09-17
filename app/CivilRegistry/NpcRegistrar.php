<?php

namespace App\CivilRegistry;

use App\Cases\WorkCaseKind;
use App\Models\WorkCase;

class NpcRegistrar
{
    public function __construct(private readonly ApplicationDecider $decider) {}

    public function workDue(): int
    {
        $worked = 0;

        WorkCase::query()
            ->dueForNpc()
            ->where('kind', WorkCaseKind::Application)
            ->with(['civilApplication.applicant.household', 'civilApplication.partner.household', 'position'])
            ->lazyById()
            ->each(function (WorkCase $workCase) use (&$worked): void {
                $application = $workCase->civilApplication;

                if ($application !== null && ! $application->isDecided()) {
                    $this->decider->decideForNpc($application, $workCase->position);
                    $worked++;
                }
            });

        return $worked;
    }
}
