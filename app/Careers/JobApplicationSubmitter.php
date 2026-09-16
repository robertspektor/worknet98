<?php

namespace App\Careers;

use App\Careers\Events\JobApplicationSubmitted;
use App\Game\ActionRefused;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JobApplicationSubmitter
{
    public function __construct(private readonly ApplicationEligibility $eligibility) {}

    public function submit(User $player, JobOpening $opening, ?string $message): JobApplication
    {
        $application = DB::transaction(function () use ($player, $opening, $message): JobApplication {
            User::query()->whereKey($player->id)->lockForUpdate()->first();

            $this->ensureEligible($player, $opening);

            return $player->jobApplications()->create([
                'job_opening_id' => $opening->id,
                'message' => $message,
                'status' => JobApplicationStatus::Pending,
                'responds_at' => now()->addSeconds($this->responseDelaySeconds()),
            ]);
        });

        JobApplicationSubmitted::dispatch($application);

        return $application;
    }

    private function ensureEligible(User $player, JobOpening $opening): void
    {
        $refusal = $this->eligibility->refusalFor($player, $opening);

        if ($refusal !== null) {
            throw new ActionRefused($refusal);
        }
    }

    private function responseDelaySeconds(): int
    {
        /** @var int */
        return config('game.job_application_response_delay_seconds');
    }
}
