<?php

namespace App\Careers;

use App\Models\JobOpening;
use App\Models\User;

class ApplicationEligibility
{
    public function refusalFor(User $player, JobOpening $opening): ?ApplicationRefusal
    {
        return match (true) {
            $opening->company->locale !== $player->locale => ApplicationRefusal::LanguageMismatch,
            $player->employment()->exists() => ApplicationRefusal::AlreadyEmployed,
            $player->jobApplications()->where('job_opening_id', $opening->id)->exists() => ApplicationRefusal::AlreadyApplied,
            $player->jobApplications()->pending()->exists() => ApplicationRefusal::ApplicationPending,
            default => null,
        };
    }
}
