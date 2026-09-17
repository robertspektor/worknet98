<?php

namespace App\CivilRegistry;

use App\Cases\CaseResolver;
use App\Game\ActionRefused;
use App\Models\CivilApplication;
use App\Models\Employment;
use App\Models\Position;
use App\Models\User;
use App\Work\DutyCheck;
use Illuminate\Support\Facades\DB;

class ApplicationDecider
{
    public function __construct(
        private readonly DutyCheck $dutyCheck,
        private readonly ApplicationCheck $check,
        private readonly CaseResolver $resolver,
        private readonly RegistrationRecorder $recorder,
    ) {}

    public function decide(User $player, CivilApplication $application, ApplicationDecision $decision): CivilApplication
    {
        $employment = $this->dutyCheck->employmentOnDuty($player);

        return $this->record($application, $decision, $employment->position, $employment);
    }

    public function decideForNpc(CivilApplication $application, Position $position): CivilApplication
    {
        $decision = $this->check->matchesRegistry($application) ? ApplicationDecision::Approved : ApplicationDecision::Rejected;

        return $this->record($application, $decision, $position, null);
    }

    private function record(CivilApplication $application, ApplicationDecision $decision, Position $position, ?Employment $employment): CivilApplication
    {
        return DB::transaction(function () use ($application, $decision, $position, $employment): CivilApplication {
            $isPending = CivilApplication::query()->whereKey($application->id)->whereNull('decision')->lockForUpdate()->exists();

            if (! $isPending) {
                throw new ActionRefused(ApplicationRefusal::AlreadyDecided);
            }

            $application->update([
                'decision' => $decision,
                'matched_registry' => $this->check->matchesRegistry($application),
                'decided_at' => now(),
                'decided_by_employment_id' => $employment?->id,
                'decided_by_position_id' => $position->id,
            ]);

            $this->resolveCaseOf($application);

            if ($decision === ApplicationDecision::Approved) {
                $this->recorder->record($application);
            }

            return $application;
        });
    }

    private function resolveCaseOf(CivilApplication $application): void
    {
        $workCase = $application->workCase;

        if ($workCase !== null) {
            $workCase->setRelation('civilApplication', $application);
            $this->resolver->resolve($workCase);
        }
    }
}
