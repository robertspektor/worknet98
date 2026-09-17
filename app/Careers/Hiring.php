<?php

namespace App\Careers;

use App\Careers\Events\PlayerHired;
use App\Mailbox\Mailbox;
use App\Models\Employment;
use App\Models\JobApplication;
use App\Models\Position;
use App\Organization\Vacancies;
use Illuminate\Support\Facades\DB;

class Hiring
{
    public function __construct(
        private readonly Vacancies $vacancies,
        private readonly OfferLetter $offerLetter,
        private readonly RejectionLetter $rejectionLetter,
        private readonly Mailbox $mailbox,
    ) {}

    public function hire(JobApplication $application): void
    {
        $employment = DB::transaction(fn (): ?Employment => $this->answerIfPending($application));

        if ($employment !== null) {
            PlayerHired::dispatch($employment);
        }
    }

    private function answerIfPending(JobApplication $application): ?Employment
    {
        $isPending = JobApplication::query()->whereKey($application->id)->pending()->lockForUpdate()->exists();

        if (! $isPending) {
            return null;
        }

        $position = $this->vacancies->claimFor($application->jobOpening);

        if ($position === null) {
            $this->reject($application);

            return null;
        }

        return $this->accept($application, $position);
    }

    private function accept(JobApplication $application, Position $position): Employment
    {
        $application->update(['status' => JobApplicationStatus::Accepted, 'responded_at' => now()]);
        $employment = $this->employ($application, $position);
        $this->mailbox->deliver($application->user, $this->offerLetter->compose($application));

        return $employment;
    }

    private function reject(JobApplication $application): void
    {
        $application->update(['status' => JobApplicationStatus::Rejected, 'responded_at' => now()]);
        $this->mailbox->deliver($application->user, $this->rejectionLetter->compose($application));
    }

    private function employ(JobApplication $application, Position $position): Employment
    {
        return Employment::create([
            'user_id' => $application->user_id,
            'company_id' => $application->jobOpening->company_id,
            'job_opening_id' => $application->job_opening_id,
            'position_id' => $position->id,
            'daily_salary' => $application->jobOpening->daily_salary,
            'hired_at' => now(),
        ]);
    }
}
