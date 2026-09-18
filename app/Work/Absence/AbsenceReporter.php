<?php

namespace App\Work\Absence;

use App\Models\Email;
use App\Models\Employment;
use App\Models\WorkCase;
use App\Work\Wallet;
use Carbon\CarbonImmutable;

class AbsenceReporter
{
    private const HOURS_PER_DAY = 24;

    public function __construct(private readonly Wallet $wallet) {}

    public function since(Employment $employment, CarbonImmutable $lastActiveAt): AbsenceReport
    {
        return new AbsenceReport(
            awayDays: max(1, (int) floor($lastActiveAt->diffInHours(now()) / self::HOURS_PER_DAY)),
            waitingCases: WorkCase::query()->open()->where('employment_id', $employment->id)->count(),
            takenOverCases: WorkCase::query()->where('taken_over_from_employment_id', $employment->id)->where('taken_over_at', '>=', $lastActiveAt)->count(),
            newMails: Email::query()->where('user_id', $employment->user_id)->where('received_at', '>=', $lastActiveAt)->count(),
            balance: $this->wallet->balanceOf($employment->user),
        );
    }
}
