<?php

namespace App\Workplace;

use App\Game\ActionRefused;
use App\Models\Appointment;
use App\Models\Employment;
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
        return $this->record($request, $this->dutyCheck->employmentOnDuty($player));
    }

    public function bookForOffice(AppointmentRequest $request): Appointment
    {
        return $this->record($request, null);
    }

    public function cancel(User $player, Appointment $appointment): void
    {
        $this->dutyCheck->employmentOnDuty($player);
        $appointment->delete();

        AppointmentCancelled::dispatch($appointment);
    }

    private function record(AppointmentRequest $request, ?Employment $bookedBy): Appointment
    {
        $appointment = DB::transaction(function () use ($request, $bookedBy): Appointment {
            Technician::query()->whereKey($request->technician->id)->lockForUpdate()->first();
            $this->ensureBookable($request);

            return Appointment::create([
                'branch_id' => $request->technician->branch_id,
                'booked_by_employment_id' => $bookedBy?->id,
                'customer_id' => $request->customer->id,
                'technician_id' => $request->technician->id,
                'date' => $request->date->toDateString(),
                'slot' => $request->slot,
            ]);
        });

        AppointmentBooked::dispatch($appointment);

        return $appointment;
    }

    private function ensureBookable(AppointmentRequest $request): void
    {
        $refusal = match (true) {
            ! $this->window->contains($request->date) => BookingRefusal::OutsideBookingWindow,
            $request->technician->isBusyAt($request->date, $request->slot) => BookingRefusal::TechnicianBusy,
            $request->technician->isBookedAt($request->date, $request->slot) => BookingRefusal::SlotTaken,
            default => null,
        };

        if ($refusal !== null) {
            throw new ActionRefused($refusal);
        }
    }
}
