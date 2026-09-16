<?php

namespace App\Careers;

use App\Careers\Events\PlayerHired;
use App\Mailbox\Mailbox;
use App\Models\Employment;
use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;

class Hiring
{
    public function __construct(
        private readonly OfferLetter $offerLetter,
        private readonly Mailbox $mailbox,
    ) {}

    public function hire(JobApplication $application): void
    {
        $employment = DB::transaction(fn (): ?Employment => $this->acceptIfPending($application));

        if ($employment !== null) {
            PlayerHired::dispatch($employment);
        }
    }

    private function acceptIfPending(JobApplication $application): ?Employment
    {
        $isPending = JobApplication::query()->whereKey($application->id)->pending()->lockForUpdate()->exists();

        if (! $isPending) {
            return null;
        }

        $application->update(['status' => JobApplicationStatus::Accepted, 'responded_at' => now()]);
        $employment = $this->employ($application);
        $this->mailbox->deliver($application->user, $this->offerLetter->compose($application));

        return $employment;
    }

    private function employ(JobApplication $application): Employment
    {
        return Employment::create([
            'user_id' => $application->user_id,
            'company_id' => $application->jobOpening->company_id,
            'job_opening_id' => $application->job_opening_id,
            'daily_salary' => $application->jobOpening->daily_salary,
            'hired_at' => now(),
        ]);
    }
}
