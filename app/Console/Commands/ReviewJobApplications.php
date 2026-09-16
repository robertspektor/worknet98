<?php

namespace App\Console\Commands;

use App\Careers\JobApplicationReviewer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('careers:review-applications')]
#[Description('Answer job applications whose response time has come')]
class ReviewJobApplications extends Command
{
    public function handle(JobApplicationReviewer $reviewer): int
    {
        $this->info("Reviewed {$reviewer->reviewDue()} job application(s).");

        return self::SUCCESS;
    }
}
