<?php

namespace App\Work;

use App\Game\ActionRefused;
use App\Models\Employment;
use App\Models\Shift;
use App\Models\User;
use App\Work\Events\ShiftEnded;
use App\Work\Events\ShiftStarted;
use Illuminate\Support\Facades\DB;

class ShiftClock
{
    public function __construct(
        private readonly TodaysShift $todaysShift,
        private readonly WorkDay $workDay,
        private readonly SalaryPayout $payout,
    ) {}

    public function clockIn(User $player): Shift
    {
        $shift = DB::transaction(function () use ($player): Shift {
            $employment = $this->lockedEmploymentOf($player);
            $this->ensureNoShiftToday($employment);

            return Shift::create([
                'user_id' => $player->id,
                'employment_id' => $employment->id,
                'work_date' => $this->workDay->today(),
                'clocked_in_at' => now(),
            ]);
        });

        ShiftStarted::dispatch($shift);

        return $shift;
    }

    public function clockOut(User $player): Shift
    {
        $shift = DB::transaction(function () use ($player): Shift {
            $shift = $this->onDutyShiftOf($this->lockedEmploymentOf($player));
            $shift->update(['clocked_out_at' => now()]);
            $this->payout->payFor($shift);

            return $shift;
        });

        ShiftEnded::dispatch($shift);

        return $shift;
    }

    private function lockedEmploymentOf(User $player): Employment
    {
        User::query()->whereKey($player->id)->lockForUpdate()->first();

        return $player->employment()->first() ?? throw new ActionRefused(ShiftRefusal::NotEmployed);
    }

    private function ensureNoShiftToday(Employment $employment): void
    {
        $shift = $this->todaysShift->of($employment);

        if ($shift !== null) {
            throw new ActionRefused($shift->isOnDuty() ? ShiftRefusal::AlreadyOnDuty : ShiftRefusal::ShiftDone);
        }
    }

    private function onDutyShiftOf(Employment $employment): Shift
    {
        $shift = $this->todaysShift->of($employment);

        if ($shift === null || ! $shift->isOnDuty()) {
            throw new ActionRefused(ShiftRefusal::NotOnDuty);
        }

        return $shift;
    }
}
