<?php

namespace App\Workplace;

use App\Game\ActionRefused;
use App\Models\Appointment;
use App\Models\Technician;
use App\Models\User;
use App\Work\DutyCheck;
use App\Workplace\Events\AppointmentBooked;
use App\Workplace\Events\AppointmentCancelled;
use Illuminate\Support\Facades\DB;

class AppointmentBooker
{
    public function __construct(
        private readonly DutyCheck $dutyCheck,
        private readonly BookingWindow $window,
    ) {}

    public function book(User $player, AppointmentRequest $request): Appointment
    {
        $appointment = DB::transaction(function () use ($player, $request): Appointment {
            $employment = $this->dutyCheck->employmentOnDuty($player);
            Technician::query()->whereKey($request->technician->id)->lockForUpdate()->first();
            $this->ensureBookable($request);

            return Appointment::create([
                'branch_id' => $request->technician->branch_id,
                'booked_by_employment_id' => $employment->id,
                'customer_id' => $request->customer->id,
                'technician_id' => $request->technician->id,
                'date' => $request->date->toDateString(),
                'slot' => $request->slot,
            ]);
        });

        AppointmentBooked::dispatch($appointment);

        return $appointment;
    }

    public function cancel(User $player, Appointment $appointment): void
    {
        $this->dutyCheck->employmentOnDuty($player);
        $appointment->delete();

        AppointmentCancelled::dispatch($appointment);
    }

    private function ensureBookable(AppointmentRequest $request): void
    {
        $refusal = match (true) {
            ! $this->window->contains($request->date) => BookingRefusal::OutsideBookingWindow,
            $request->technician->isBusyAt($request->date, $request->slot) => BookingRefusal::TechnicianBusy,
            $this->isTaken($request) => BookingRefusal::SlotTaken,
            default => null,
        };

        if ($refusal !== null) {
            throw new ActionRefused($refusal);
        }
    }

    private function isTaken(AppointmentRequest $request): bool
    {
        return Appointment::query()
            ->whereBelongsTo($request->technician)
            ->whereDate('date', $request->date->toDateString())
            ->where('slot', $request->slot)
            ->exists();
    }
}
