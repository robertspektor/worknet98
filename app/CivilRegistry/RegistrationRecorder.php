<?php

namespace App\CivilRegistry;

use App\Models\CivilApplication;
use App\Models\Household;

class RegistrationRecorder
{
    public function record(CivilApplication $application): void
    {
        match ($application->kind) {
            ApplicationKind::Move => $this->move($application),
            ApplicationKind::Marriage => $this->marry($application),
            ApplicationKind::NameChange => $this->rename($application),
            ApplicationKind::PetRegistration => null,
        };
    }

    private function move(CivilApplication $application): void
    {
        $household = $application->applicant->household;
        $isTaken = Household::query()
            ->where('city_id', $household->city_id)
            ->where('district', $application->new_district)
            ->where('street', $application->new_street)
            ->exists();

        if (! $isTaken) {
            $household->update(['district' => $application->new_district, 'street' => $application->new_street]);
        }
    }

    private function rename(CivilApplication $application): void
    {
        if ($application->detail !== null) {
            $application->applicant->update(['name' => $application->detail]);
        }
    }

    private function marry(CivilApplication $application): void
    {
        $partner = $application->partner;

        if ($partner === null || $partner->household_id === $application->applicant->household_id) {
            return;
        }

        $formerHousehold = $partner->household;
        $partner->update(['household_id' => $application->applicant->household_id]);

        if (! $formerHousehold->members()->exists()) {
            $formerHousehold->delete();
        }
    }
}
