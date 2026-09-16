<?php

namespace App\Workplace;

use App\Game\ActionRefused;
use App\Models\Appointment;
use App\Models\Employment;
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
            Employment::query()->whereKey($employment->id)->lockForUpdate()->first();
            $this->ensureBookable($employment, $request);

            return $employment->appointments()->create([
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

    private function ensureBookable(Employment $employment, AppointmentRequest $request): void
    {
        $refusal = match (true) {
            ! $this->window->contains($request->date) => BookingRefusal::OutsideBookingWindow,
            $request->technician->isBusyAt($request->date, $request->slot) => BookingRefusal::TechnicianBusy,
            $this->isTaken($employment, $request) => BookingRefusal::SlotTaken,
            default => null,
        };

        if ($refusal !== null) {
            throw new ActionRefused($refusal);
        }
    }

    private function isTaken(Employment $employment, AppointmentRequest $request): bool
    {
        return $employment->appointments()
            ->where('technician_id', $request->technician->id)
            ->whereDate('date', $request->date->toDateString())
            ->where('slot', $request->slot)
            ->exists();
    }
}
