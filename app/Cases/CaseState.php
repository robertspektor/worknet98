<?php

namespace App\Cases;

use App\Mailbox\EmailAction;
use App\Models\Appointment;
use App\Models\CalendarEntry;
use App\Models\CivilApplication;
use App\Models\Customer;
use App\Models\Email;
use App\Models\Shipment;
use App\Workplace\BookingWindow;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

readonly class CaseState
{
    /**
     * @param  Collection<int, Appointment>  $appointments
     * @param  Collection<int, Email>  $sentEmails
     * @param  Collection<int, CalendarEntry>  $calendarEntries
     * @param  Collection<string, Customer>  $customers
     */
    public function __construct(
        private CarbonImmutable $openedOn,
        private Collection $appointments,
        private Collection $sentEmails,
        private Collection $calendarEntries,
        private Collection $customers,
        private BookingWindow $window,
        private ?Shipment $shipment = null,
        private ?CivilApplication $civilApplication = null,
    ) {}

    public function civilApplication(): ?CivilApplication
    {
        return $this->civilApplication;
    }

    public function shipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function appointmentFor(string $customerSlug): ?Appointment
    {
        $customer = $this->customers->get($customerSlug);

        return $this->appointments
            ->where('customer_id', $customer?->id)
            ->whereNull('failed_at')
            ->sortByDesc('id')
            ->first();
    }

    public function hasSentEmail(string $customerSlug, EmailAction $action): bool
    {
        $address = $this->customers->get($customerSlug)?->emailAddress();

        return $this->sentEmails->contains(
            fn (Email $email): bool => $email->recipient_address === $address && $email->action === $action,
        );
    }

    public function hasCalendarEntryAt(CarbonImmutable $date, string $time): bool
    {
        return $this->calendarEntries->contains(
            fn (CalendarEntry $entry): bool => $entry->date->isSameDay($date) && $entry->time === $time,
        );
    }

    public function workDaysSinceOpening(CarbonImmutable $date): int
    {
        return $this->window->workDaysBetween($this->openedOn, $date);
    }
}
