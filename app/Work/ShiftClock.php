<?php

namespace App\Work;

use App\Game\ActionRefused;
use App\Models\Employment;
use App\Models\Shift;
use App\Models\User;
use App\Work\Events\ShiftEnded;
use App\Work\Events\ShiftStarted;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class ShiftClock
{
    public function __construct(
        private readonly OpenShift $openShift,
        private readonly ShiftActivity $activity,
    ) {}

    public function clockIn(User $player): Shift
    {
        $shift = DB::transaction(function () use ($player): Shift {
            $employment = $this->lockedEmploymentOf($player);

            if ($this->openShift->of($employment) !== null) {
                throw new ActionRefused(ShiftRefusal::AlreadyOnDuty);
            }

            return Shift::create([
                'user_id' => $player->id,
                'employment_id' => $employment->id,
                'clocked_in_at' => now(),
                'last_active_at' => now(),
            ]);
        });

        ShiftStarted::dispatch($shift);

        return $shift;
    }

    public function heartbeat(User $player): Shift
    {
        return DB::transaction(function () use ($player): Shift {
            $shift = $this->onDutyShiftOf($this->lockedEmploymentOf($player));
            $this->activity->recordAt($shift, CarbonImmutable::now());

            return $shift;
        });
    }

    public function clockOut(User $player): Shift
    {
        $shift = DB::transaction(function () use ($player): Shift {
            $shift = $this->onDutyShiftOf($this->lockedEmploymentOf($player));
            $this->activity->recordAt($shift, CarbonImmutable::now());
            $shift->update(['clocked_out_at' => now()]);

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

    private function onDutyShiftOf(Employment $employment): Shift
    {
        return $this->openShift->of($employment) ?? throw new ActionRefused(ShiftRefusal::NotOnDuty);
    }
}
