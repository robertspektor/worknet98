<?php

namespace App\Careers;

use App\Models\JobOpening;
use App\Models\User;
use App\Organization\Vacancies;

class ApplicationEligibility
{
    public function __construct(private readonly Vacancies $vacancies) {}

    public function refusalFor(User $player, JobOpening $opening): ?ApplicationRefusal
    {
        return match (true) {
            ! $opening->is_open => ApplicationRefusal::PositionClosed,
            $opening->company->locale !== $player->locale => ApplicationRefusal::LanguageMismatch,
            $player->employment()->exists() => ApplicationRefusal::AlreadyEmployed,
            $player->jobApplications()->where('job_opening_id', $opening->id)->exists() => ApplicationRefusal::AlreadyApplied,
            $player->jobApplications()->pending()->exists() => ApplicationRefusal::ApplicationPending,
            ! $this->vacancies->existFor($opening) => ApplicationRefusal::NoVacancy,
            default => null,
        };
    }
}
