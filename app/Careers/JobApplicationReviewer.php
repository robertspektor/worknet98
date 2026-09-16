<?php

namespace App\Careers;

use App\Models\JobApplication;

class JobApplicationReviewer
{
    public function __construct(private readonly Hiring $hiring) {}

    public function reviewDue(): int
    {
        $reviewed = 0;

        JobApplication::query()
            ->dueForResponse()
            ->with(['user', 'jobOpening.company'])
            ->lazyById()
            ->each(function (JobApplication $application) use (&$reviewed): void {
                $this->hiring->hire($application);
                $reviewed++;
            });

        return $reviewed;
    }
}
