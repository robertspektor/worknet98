<?php

namespace App\Careers;

use App\Models\JobApplication;

class JobApplicationReviewer
{
    public function __construct(
        private readonly Hiring $hiring,
        private readonly CandidateRanking $ranking,
    ) {}

    public function reviewDue(): int
    {
        $reviewed = 0;

        $due = JobApplication::query()
            ->dueForResponse()
            ->with(['user', 'jobOpening.company'])
            ->get();

        foreach ($due->groupBy('job_opening_id') as $candidates) {
            foreach ($this->ranking->sort($candidates) as $application) {
                $this->hiring->hire($application);
                $reviewed++;
            }
        }

        return $reviewed;
    }
}
