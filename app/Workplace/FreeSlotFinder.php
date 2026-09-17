<?php

namespace App\Workplace;

use App\Models\Customer;
use App\Models\Technician;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class FreeSlotFinder
{
    public function __construct(private readonly BookingWindow $window) {}

    public function earliestFor(Customer $customer, string $skill): ?AppointmentRequest
    {
        $technicians = $this->techniciansWith($customer, $skill);

        foreach ($this->window->days() as $date) {
            foreach ($this->slotsFor($customer) as $slot) {
                $technician = $technicians->first(fn (Technician $technician): bool => $this->isFree($technician, $date, $slot));

                if ($technician !== null) {
                    return new AppointmentRequest(customer: $customer, technician: $technician, date: $date, slot: $slot);
                }
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Technician>
     */
    private function techniciansWith(Customer $customer, string $skill): Collection
    {
        return $customer->branch->technicians()
            ->orderBy('id')
            ->get()
            ->filter(fn (Technician $technician): bool => $technician->hasSkill($skill))
            ->values();
    }

    /**
     * @return list<string>
     */
    private function slotsFor(Customer $customer): array
    {
        return array_values(array_filter(ServiceSlots::ALL, $customer->availability->includes(...)));
    }

    private function isFree(Technician $technician, CarbonImmutable $date, string $slot): bool
    {
        return ! $technician->isBusyAt($date, $slot) && ! $technician->isBookedAt($date, $slot);
    }
}
