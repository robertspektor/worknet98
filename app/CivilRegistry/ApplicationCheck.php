<?php

namespace App\CivilRegistry;

use App\Models\CivilApplication;
use App\Models\Household;

class ApplicationCheck
{
    public function matchesRegistry(CivilApplication $application): bool
    {
        return $this->livesAt($application->applicant->household, $application->claimed_district, $application->claimed_street)
            && ($application->partner === null
                || $this->livesAt($application->partner->household, $application->claimed_partner_district, $application->claimed_partner_street));
    }

    private function livesAt(Household $household, ?string $district, ?string $street): bool
    {
        return $household->district === $district && $household->street === $street;
    }
}
