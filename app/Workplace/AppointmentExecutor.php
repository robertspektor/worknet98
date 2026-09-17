<?php

namespace App\Workplace;

use App\Cases\CaseResolver;
use App\Game\GameClock;
use App\Models\Appointment;
use App\Models\WorkCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AppointmentExecutor
{
    private const VISIT_HOURS = 2;

    public function __construct(
        private readonly GameClock $clock,
        private readonly CaseResolver $resolver,
    ) {}

    public function executeDue(): int
    {
        $executed = 0;

        $this->dueAppointments()->lazyById()->each(function (Appointment $appointment) use (&$executed): void {
            if ($this->execute($appointment)) {
                $executed++;
            }
        });

        return $executed;
    }

    /**
     * @return Builder<Appointment>
     */
    private function dueAppointments(): Builder
    {
        $visitStartedBefore = $this->clock->now()->subHours(self::VISIT_HOURS);

        return Appointment::query()
            ->whereNull('executed_at')
            ->where(fn (Builder $query) => $query
                ->whereDate('date', '<', $visitStartedBefore->toDateString())
                ->orWhere(fn (Builder $query) => $query
                    ->whereDate('date', $visitStartedBefore->toDateString())
                    ->where('slot', '<=', $visitStartedBefore->format('H:i'))));
    }

    private function execute(Appointment $appointment): bool
    {
        return DB::transaction(function () use ($appointment): bool {
            $isPending = Appointment::query()->whereKey($appointment->id)->whereNull('executed_at')->lockForUpdate()->exists();

            if (! $isPending) {
                return false;
            }

            $appointment->update(['executed_at' => now()]);
            $this->resolveCaseOf($appointment);

            return true;
        });
    }

    private function resolveCaseOf(Appointment $appointment): void
    {
        $workCase = WorkCase::query()->where('customer_id', $appointment->customer_id)->open()->oldest('id')->first();

        if ($workCase !== null) {
            $this->resolver->resolve($workCase);
        }
    }
}
